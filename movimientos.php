<?php
require_once 'db.php';
require_once 'includes/header.php';

/* =========================
   MOVIMIENTOS + VALIDACIÓN
========================= */

$sql = "
SELECT
    mm.fecha_movimiento,
    m.codigo,
    m.activo,

    mm.tipo_movimiento,
    mm.observaciones,

    uo.nombre AS usuario_origen,
    ud.nombre AS usuario_destino,

    ubo.nombre AS ubicacion_origen,
    ubd.nombre AS ubicacion_destino,

    vd.resultado AS validacion

FROM movimiento_material mm

JOIN material m
    ON mm.id_material = m.id_material

LEFT JOIN usuario uo
    ON mm.id_usuario_origen = uo.id_usuario

LEFT JOIN usuario ud
    ON mm.id_usuario_destino = ud.id_usuario

LEFT JOIN ubicacion ubo
    ON mm.id_ubicacion_origen = ubo.id_ubicacion

LEFT JOIN ubicacion ubd
    ON mm.id_ubicacion_destino = ubd.id_ubicacion

LEFT JOIN validacion_devolucion vd
    ON mm.id_movimiento = vd.id_movimiento

ORDER BY mm.fecha_movimiento DESC
";

$movimientos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Historial Global de Movimientos</h2>

<p>
    <a href="index.php">← Volver al Dashboard</a>
</p>

<table border="1" cellpadding="6">

<tr>
    <th>Fecha</th>
    <th>Material</th>
    <th>Tipo</th>
    <th>Origen</th>
    <th>Destino</th>
    <th>Observación</th>
    <th>Estado</th>
    <th>Validación</th>
    <th>Ver</th>
</tr>

<?php foreach ($movimientos as $m): ?>

<?php
$origen = [];

if (!empty($m['usuario_origen'])) {
    $origen[] = $m['usuario_origen'];
}

if (!empty($m['ubicacion_origen'])) {
    $origen[] = $m['ubicacion_origen'];
}

$destino = [];

if (!empty($m['usuario_destino'])) {
    $destino[] = $m['usuario_destino'];
}

if (!empty($m['ubicacion_destino'])) {
    $destino[] = $m['ubicacion_destino'];
}
?>

<tr>

    <td>
        <?= htmlspecialchars($m['fecha_movimiento']) ?>
    </td>

    <td>
        <?= htmlspecialchars($m['codigo']) ?>
    </td>

    <td>
        <?= htmlspecialchars($m['tipo_movimiento']) ?>
    </td>

    <td>
        <?= !empty($origen)
            ? htmlspecialchars(implode(' / ', $origen))
            : '—'; ?>
    </td>

    <td>
        <?= !empty($destino)
            ? htmlspecialchars(implode(' / ', $destino))
            : '—'; ?>
    </td>

    <td>
        <?= htmlspecialchars($m['observaciones'] ?? '') ?>
    </td>

    <td>
        <?php if ($m['activo']) : ?>
            <span style="color:green;">Activo</span>
        <?php else : ?>
            <span style="color:red;">Baja</span>
        <?php endif; ?>
    </td>

    <td>
        <?php if (!empty($m['validacion'])): ?>

            <?php if ($m['validacion'] === 'APROBADA'): ?>
                <span style="color:green;">APROBADA</span>
            <?php else: ?>
                <span style="color:red;">RECHAZADA</span>
            <?php endif; ?>

        <?php else: ?>
            <span style="color:#999;">Pendiente</span>
        <?php endif; ?>
    </td>

    <td>
        <a href="material.php?codigo=<?= urlencode($m['codigo']) ?>">
            Ver
        </a>
    </td>

</tr>

<?php endforeach; ?>

</table>

<?php require_once 'includes/footer.php'; ?>