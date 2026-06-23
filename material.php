<?php
require_once 'db.php';
require_once 'includes/header.php';

/* =========================
   VALIDAR ENTRADA
========================= */

$codigo = $_GET['codigo'] ?? null;

if (!$codigo) {
    echo "<p>Material no especificado.</p>";
    require_once 'includes/footer.php';
    exit;
}

/* =========================
   DATOS DEL MATERIAL
========================= */

$sql = "
SELECT
    m.id_material,
    m.codigo,
    m.descripcion,
    m.categoria,
    m.marca,
    m.modelo,
    m.activo,

    em.nombre AS estado,
    u.nombre AS usuario,
    ub.nombre AS ubicacion

FROM material m

LEFT JOIN estado_actual_material eam
    ON m.id_material = eam.id_material

LEFT JOIN estado_material em
    ON eam.id_estado = em.id_estado

LEFT JOIN usuario u
    ON eam.id_usuario_actual = u.id_usuario

LEFT JOIN ubicacion ub
    ON eam.id_ubicacion_actual = ub.id_ubicacion

WHERE m.codigo = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$codigo]);
$material = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$material) {
    echo "<p>Material no encontrado.</p>";
    require_once 'includes/footer.php';
    exit;
}

/* =========================
   HISTORIAL MOVIMIENTOS
========================= */

$sql2 = "
SELECT
    mm.id_movimiento,
    mm.fecha_movimiento,
    mm.tipo_movimiento,
    mm.observaciones,

    uo.nombre AS usuario_origen,
    ud.nombre AS usuario_destino,

    ubo.nombre AS ubicacion_origen,
    ubd.nombre AS ubicacion_destino

FROM movimiento_material mm

LEFT JOIN usuario uo
    ON mm.id_usuario_origen = uo.id_usuario

LEFT JOIN usuario ud
    ON mm.id_usuario_destino = ud.id_usuario

LEFT JOIN ubicacion ubo
    ON mm.id_ubicacion_origen = ubo.id_ubicacion

LEFT JOIN ubicacion ubd
    ON mm.id_ubicacion_destino = ubd.id_ubicacion

WHERE mm.id_material = ?

ORDER BY mm.fecha_movimiento DESC
";

$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([$material['id_material']]);
$movimientos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   VALIDACIONES
========================= */

$sql3 = "
SELECT
    vd.fecha_validacion,
    vd.resultado,
    vd.observaciones,
    u.nombre AS validador,
    mm.tipo_movimiento
FROM validacion_devolucion vd
JOIN movimiento_material mm
    ON vd.id_movimiento = mm.id_movimiento
LEFT JOIN usuario u
    ON vd.id_validador = u.id_usuario
WHERE mm.id_material = ?
ORDER BY vd.fecha_validacion DESC
";

$stmt3 = $pdo->prepare($sql3);
$stmt3->execute([$material['id_material']]);
$validaciones = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Detalle de Material</h2>

<p><b>Código:</b> <?= htmlspecialchars($material['codigo']) ?></p>
<p><b>Descripción:</b> <?= htmlspecialchars($material['descripcion']) ?></p>
<p><b>Categoría:</b> <?= htmlspecialchars($material['categoria'] ?? '-') ?></p>
<p><b>Marca:</b> <?= htmlspecialchars($material['marca'] ?? '-') ?></p>
<p><b>Modelo:</b> <?= htmlspecialchars($material['modelo'] ?? '-') ?></p>

<hr>

<h3>Estado Actual</h3>

<p><b>Estado:</b> <?= htmlspecialchars($material['estado'] ?? '-') ?></p>
<p><b>Ubicación:</b> <?= htmlspecialchars($material['ubicacion'] ?? '-') ?></p>
<p><b>Responsable:</b>
    <?= $material['usuario']
        ? htmlspecialchars($material['usuario'])
        : 'Sin asignar'; ?>
</p>

<p><b>Estado administrativo:</b>
    <?= $material['activo']
        ? '<span style="color:green;">Activo</span>'
        : '<span style="color:red;">Dado de baja</span>'; ?>
</p>

<hr>

<h3>Historial de Movimientos</h3>

<?php if (empty($movimientos)): ?>
    <p>No existen movimientos registrados.</p>
<?php else: ?>

<table>
    <tr>
        <th>Fecha</th>
        <th>Tipo</th>
        <th>Origen</th>
        <th>Destino</th>
        <th>Observaciones</th>
    </tr>

    <?php foreach ($movimientos as $mov): ?>

    <?php
    $origen = [];
    if ($mov['usuario_origen']) $origen[] = $mov['usuario_origen'];
    if ($mov['ubicacion_origen']) $origen[] = $mov['ubicacion_origen'];

    $destino = [];
    if ($mov['usuario_destino']) $destino[] = $mov['usuario_destino'];
    if ($mov['ubicacion_destino']) $destino[] = $mov['ubicacion_destino'];
    ?>

    <tr>
        <td><?= htmlspecialchars($mov['fecha_movimiento']) ?></td>
        <td><?= htmlspecialchars($mov['tipo_movimiento']) ?></td>
        <td><?= $origen ? htmlspecialchars(implode(' / ', $origen)) : '—' ?></td>
        <td><?= $destino ? htmlspecialchars(implode(' / ', $destino)) : '—' ?></td>
        <td><?= htmlspecialchars($mov['observaciones'] ?? '') ?></td>
    </tr>

    <?php endforeach; ?>
</table>

<?php endif; ?>

<hr>

<h3>Historial de Validaciones</h3>

<?php if (empty($validaciones)): ?>
    <p>No existen validaciones registradas.</p>
<?php else: ?>

<table>
    <tr>
        <th>Fecha</th>
        <th>Validador</th>
        <th>Movimiento</th>
        <th>Resultado</th>
        <th>Observaciones</th>
    </tr>

    <?php foreach ($validaciones as $v): ?>
    <tr>
        <td><?= htmlspecialchars($v['fecha_validacion']) ?></td>
        <td><?= htmlspecialchars($v['validador']) ?></td>
        <td><?= htmlspecialchars($v['tipo_movimiento']) ?></td>
        <td>
            <?= $v['resultado'] === 'APROBADA'
                ? '<span style="color:green;">APROBADA</span>'
                : '<span style="color:red;">RECHAZADA</span>'; ?>
        </td>
        <td><?= htmlspecialchars($v['observaciones'] ?? '') ?></td>
    </tr>
    <?php endforeach; ?>

</table>

<?php endif; ?>

<p style="margin-top:20px;">
    <a href="index.php">← Volver al listado</a>
</p>

<?php require_once 'includes/footer.php'; ?>