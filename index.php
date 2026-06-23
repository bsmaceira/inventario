<?php
require_once 'db.php';
require_once 'includes/header.php';

$sql = "
SELECT
    m.id_material,
    m.codigo,
    m.descripcion,
    m.activo,
    em.nombre AS estado,
    u.nombre AS usuario,
    ub.nombre AS ubicacion
FROM material m
LEFT JOIN estado_actual_material eam ON m.id_material = eam.id_material
LEFT JOIN estado_material em ON eam.id_estado = em.id_estado
LEFT JOIN usuario u ON eam.id_usuario_actual = u.id_usuario
LEFT JOIN ubicacion ub ON eam.id_ubicacion_actual = ub.id_ubicacion
WHERE m.activo = 1
ORDER BY m.codigo
";

$materiales = $pdo->query($sql)->fetchAll();
?>

<h2>Inventario Actual en Stock</h2>

<p>
    <a href="nuevo_material.php">Nuevo material</a> |
    <a href="nuevo_movimiento.php">Nuevo movimiento</a> |
    <a href="movimientos.php">Historial</a>
    
</p>

<table>
    <tr>
        <th>Código</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Responsable</th>
        <th>Ubicación Física</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($materiales as $m): ?>
    <tr>
        <td><?= htmlspecialchars($m['codigo']) ?></td>
        <td><?= htmlspecialchars($m['descripcion']) ?></td>
        <td><?= htmlspecialchars($m['estado']) ?></td>
        <td><?= htmlspecialchars($m['usuario'] ?? '-') ?></td>
        <td><?= htmlspecialchars($m['ubicacion']) ?></td>

        <td>
            <a href="material.php?codigo=<?= $m['codigo'] ?>">Ver</a>

            |
            <form action="baja_material.php" method="POST" style="display:inline;">
                <input type="hidden" name="id_material" value="<?= $m['id_material'] ?>">

                <button type="submit"
                        onclick="return confirm('¿Dar de baja este material?');"
                        style="color:red; border:none; background:none; cursor:pointer;">
                    Baja
                </button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php require_once 'includes/footer.php'; ?>