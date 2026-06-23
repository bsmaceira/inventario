<?php

require_once '../db.php';

$q = $_GET['q'] ?? '';

$sql = "
SELECT
    m.codigo,
    m.descripcion,
    m.categoria,
    em.nombre AS estado,
    u.nombre AS usuario,
    ub.nombre AS ubicacion
FROM material m
LEFT JOIN estado_actual_material eam ON m.id_material = eam.id_material
LEFT JOIN estado_material em ON eam.id_estado = em.id_estado
LEFT JOIN usuario u ON eam.id_usuario_actual = u.id_usuario
LEFT JOIN ubicacion ub ON eam.id_ubicacion_actual = ub.id_ubicacion
WHERE m.activo = 1
AND (
    m.codigo LIKE :q
    OR m.descripcion LIKE :q
    OR m.categoria LIKE :q
)
ORDER BY m.codigo
LIMIT 20
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['q' => "%$q%"]);

$materiales = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($materiales);