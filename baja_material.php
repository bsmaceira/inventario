<?php
require_once 'db.php';

/* =========================
   VALIDACIÓN ID (POST)
========================= */
$id = $_POST['id_material'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

/* =========================
   1. BAJA LÓGICA
========================= */
$sql = "UPDATE material SET activo = 0 WHERE id_material = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

/* =========================
   2. MOVIMIENTO (TRAZABILIDAD)
========================= */
$sql2 = "
INSERT INTO movimiento_material
(id_material, tipo_movimiento, observaciones)
VALUES (?, 'BAJA', 'Material dado de baja desde sistema')
";

$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([$id]);

/* =========================
   3. ACTUALIZAR ESTADO ACTUAL
========================= */
$sql3 = "
UPDATE estado_actual_material
SET id_usuario_actual = NULL,
    id_estado = (
        SELECT id_estado
        FROM estado_material
        WHERE nombre = 'Disponible'
        LIMIT 1
    )
WHERE id_material = ?
";

$stmt3 = $pdo->prepare($sql3);
$stmt3->execute([$id]);

/* =========================
   REDIRECCIÓN FINAL
========================= */
header("Location: index.php");
exit;