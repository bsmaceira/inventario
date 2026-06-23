<?php
require_once 'db.php';
require_once 'includes/header.php';

/* =====================================
   Cargar datos para los selects
===================================== */

$ubicaciones = $pdo->query("
    SELECT id_ubicacion, nombre
    FROM ubicacion
    ORDER BY nombre
")->fetchAll();

$usuarios = $pdo->query("
    SELECT id_usuario, nombre
    FROM usuario
    ORDER BY nombre
")->fetchAll();

$estados = $pdo->query("
    SELECT id_estado, nombre
    FROM estado_material
    ORDER BY nombre
")->fetchAll();

/* =====================================
   Procesar formulario
===================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $pdo->beginTransaction();

        /* -----------------------------
           Crear material
        ----------------------------- */

        $sql = "
        INSERT INTO material
        (
            descripcion,
            categoria,
            marca,
            modelo
        )
        VALUES (?, ?, ?, ?)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $_POST['descripcion'],
            $_POST['categoria'],
            $_POST['marca'],
            $_POST['modelo']
        ]);

        $id_material = $pdo->lastInsertId();

        /* -----------------------------
           Generar código automático
        ----------------------------- */

        $codigo = 'MAT-' . str_pad($id_material, 3, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("
            UPDATE material
            SET codigo = ?
            WHERE id_material = ?
        ");

        $stmt->execute([
            $codigo,
            $id_material
        ]);

        /* -----------------------------
           Estado actual obligatorio
        ----------------------------- */

        $id_usuario = !empty($_POST['id_usuario'])
            ? $_POST['id_usuario']
            : null;

        $stmt = $pdo->prepare("
            INSERT INTO estado_actual_material
            (
                id_material,
                id_usuario_actual,
                id_ubicacion_actual,
                id_estado
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $id_material,
            $id_usuario,
            $_POST['id_ubicacion'],
            $_POST['id_estado']
        ]);

        /* -----------------------------
           Movimiento inicial
        ----------------------------- */

        $stmt = $pdo->prepare("
            INSERT INTO movimiento_material
            (
                id_material,
                id_usuario_destino,
                id_ubicacion_destino,
                tipo_movimiento,
                observaciones
            )
            VALUES (?, ?, ?, 'ALTA', 'Alta inicial del material')
        ");

        $stmt->execute([
            $id_material,
            $id_usuario,
            $_POST['id_ubicacion']
        ]);

        $pdo->commit();

        header("Location: index.php");
        exit;

    } catch (Exception $e) {

        $pdo->rollBack();

        echo "<p style='color:red'>Error: "
            . htmlspecialchars($e->getMessage())
            . "</p>";
    }
}
?>

<h2>Nuevo Material</h2>

<form method="POST">

    <fieldset>
        <legend>Datos del material</legend>

        <p>
            <label>Descripción</label><br>
            <input type="text" name="descripcion" required>
        </p>

        <p>
            <label>Categoría</label><br>
            <input type="text" name="categoria">
        </p>

        <p>
            <label>Marca</label><br>
            <input type="text" name="marca">
        </p>

        <p>
            <label>Modelo</label><br>
            <input type="text" name="modelo">
        </p>

    </fieldset>

    <fieldset>
        <legend>Estado inicial</legend>

        <p>
            <label>Ubicación física</label><br>

            <select name="id_ubicacion" required>
                <?php foreach ($ubicaciones as $u): ?>
                    <option value="<?= $u['id_ubicacion'] ?>">
                        <?= htmlspecialchars($u['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label>Estado</label><br>

            <select name="id_estado" required>
                <?php foreach ($estados as $e): ?>
                    <option value="<?= $e['id_estado'] ?>">
                        <?= htmlspecialchars($e['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label>Responsable (opcional)</label><br>

            <select name="id_usuario">
                <option value="">Sin asignar</option>

                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id_usuario'] ?>">
                        <?= htmlspecialchars($u['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

    </fieldset>

    <button type="submit">
        Crear material
    </button>

</form>

<?php require_once 'includes/footer.php'; ?>