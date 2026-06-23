<?php
require_once 'db.php';
require_once 'includes/header.php';

$materiales = $pdo->query("
    SELECT *
    FROM material
    WHERE activo = 1
    ORDER BY codigo
")->fetchAll();

$usuarios = $pdo->query("
    SELECT *
    FROM usuario
    WHERE activo = 1
    ORDER BY nombre
")->fetchAll();

$ubicaciones = $pdo->query("
    SELECT *
    FROM ubicacion
    ORDER BY nombre
")->fetchAll();

$estados = $pdo->query("
    SELECT *
    FROM estado_material
    ORDER BY nombre
")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_material = $_POST['id_material'];

    $tipo = $_POST['tipo_movimiento'];

    $id_usuario_origen =
        !empty($_POST['id_usuario_origen'])
        ? $_POST['id_usuario_origen']
        : null;

    $id_usuario_destino =
        !empty($_POST['id_usuario_destino'])
        ? $_POST['id_usuario_destino']
        : null;

    $id_ubicacion_origen = $_POST['id_ubicacion_origen'];
    $id_ubicacion_destino = $_POST['id_ubicacion_destino'];

    $id_estado = $_POST['id_estado'];

    /* =========================
       MOVIMIENTO
    ========================= */

    $sql = "
    INSERT INTO movimiento_material
    (
        id_material,
        id_usuario_origen,
        id_usuario_destino,
        id_ubicacion_origen,
        id_ubicacion_destino,
        tipo_movimiento,
        observaciones
    )
    VALUES (?,?,?,?,?,?,?)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $id_material,
        $id_usuario_origen,
        $id_usuario_destino,
        $id_ubicacion_origen,
        $id_ubicacion_destino,
        $tipo,
        $_POST['observaciones']
    ]);

    $id_movimiento = $pdo->lastInsertId();

    /* =========================
       ESTADO ACTUAL
    ========================= */

    $sql2 = "
    INSERT INTO estado_actual_material
    (
        id_material,
        id_usuario_actual,
        id_ubicacion_actual,
        id_estado
    )
    VALUES (?,?,?,?)

    ON DUPLICATE KEY UPDATE

        id_usuario_actual = VALUES(id_usuario_actual),
        id_ubicacion_actual = VALUES(id_ubicacion_actual),
        id_estado = VALUES(id_estado),
        fecha_actualizacion = CURRENT_TIMESTAMP
    ";

    $pdo->prepare($sql2)->execute([
        $id_material,
        $id_usuario_destino,
        $id_ubicacion_destino,
        $id_estado
    ]);

    /* =========================
       VALIDACIÓN (OPCIONAL)
    ========================= */

    if (!empty($_POST['registrar_validacion'])) {

        $sql3 = "
        INSERT INTO validacion_devolucion
        (
            id_movimiento,
            id_validador,
            resultado,
            observaciones
        )
        VALUES (?,?,?,?)
        ";

        $pdo->prepare($sql3)->execute([
            $id_movimiento,
            $_POST['id_validador'],
            $_POST['resultado_validacion'],
            $_POST['observaciones_validacion']
        ]);
    }

    header("Location: movimientos.php");
    exit;
}
?>

<h2>Nuevo Movimiento de Material</h2>

<form method="POST">

    <label>Material</label>

    <select name="id_material" required>
        <?php foreach ($materiales as $m): ?>
            <option value="<?= $m['id_material'] ?>">
                <?= htmlspecialchars($m['codigo']) ?>
                -
                <?= htmlspecialchars($m['descripcion']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Tipo de movimiento</label>

    <select name="tipo_movimiento" required>
        <option value="ASIGNACION">ASIGNACION</option>
        <option value="TRASLADO">TRASLADO</option>
        <option value="DEVOLUCION">DEVOLUCION</option>
        <option value="REPARACION">REPARACION</option>
    </select>

    <h3>Origen</h3>

    <label>Usuario origen</label>

    <select name="id_usuario_origen">
        <option value="">--</option>

        <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id_usuario'] ?>">
                <?= htmlspecialchars($u['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Ubicación origen *</label>

    <select name="id_ubicacion_origen" required>
        <?php foreach ($ubicaciones as $ub): ?>
            <option value="<?= $ub['id_ubicacion'] ?>">
                <?= htmlspecialchars($ub['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h3>Destino</h3>

    <label>Usuario destino</label>

    <select name="id_usuario_destino">
        <option value="">--</option>

        <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id_usuario'] ?>">
                <?= htmlspecialchars($u['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Ubicación destino *</label>

    <select name="id_ubicacion_destino" required>
        <?php foreach ($ubicaciones as $ub): ?>
            <option value="<?= $ub['id_ubicacion'] ?>">
                <?= htmlspecialchars($ub['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Estado</label>

    <select name="id_estado" required>
        <?php foreach ($estados as $e): ?>
            <option value="<?= $e['id_estado'] ?>">
                <?= htmlspecialchars($e['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Observaciones</label>

    <textarea name="observaciones"></textarea>

    <hr>

    <h3>Validación (Opcional)</h3>

    <label>
        <input
            type="checkbox"
            name="registrar_validacion"
            value="1">
        Registrar validación
    </label>

    <br><br>

    <label>Validador</label>

    <select name="id_validador">
        <option value="">--</option>

        <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id_usuario'] ?>">
                <?= htmlspecialchars($u['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Resultado</label>

    <select name="resultado_validacion">
        <option value="APROBADA">APROBADA</option>
        <option value="RECHAZADA">RECHAZADA</option>
    </select>

    <label>Observaciones de validación</label>

    <textarea
        name="observaciones_validacion">
    </textarea>

    <br><br>

    <button type="submit">
        Guardar movimiento
    </button>

</form>

<?php require_once 'includes/footer.php'; ?>