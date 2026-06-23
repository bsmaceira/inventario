-- ==========================================
-- ESTADOS
-- ==========================================

INSERT INTO estado_material (nombre, descripcion) VALUES
('Disponible', 'Material disponible'),
('Asignado', 'Material asignado a usuario'),
('En reparación', 'Material en reparación');

-- ==========================================
-- USUARIOS
-- ==========================================

INSERT INTO usuario (nombre, email, cargo) VALUES
('Juan Pérez', 'juan@empresa.com', 'Técnico'),
('María López', 'maria@empresa.com', 'Administradora'),
('Pedro García', 'pedro@empresa.com', 'Ingeniero'),
('Ana Fernández', 'ana@empresa.com', 'Almacén');

-- ==========================================
-- UBICACIONES
-- ==========================================

INSERT INTO ubicacion (nombre, descripcion, tipo) VALUES
('Almacén Central', 'Almacén principal', 'ALMACEN'),
('Oficina Técnica', 'Zona de trabajo', 'OFICINA'),
('Taller', 'Zona de reparación', 'TALLER');

-- ==========================================
-- MATERIALES
-- ==========================================

INSERT INTO material
(codigo, descripcion, categoria, marca, modelo)
VALUES
('MAT-001', 'Portátil Dell', 'Informática', 'Dell', 'Latitude'),
('MAT-002', 'Taladro Bosch', 'Herramienta', 'Bosch', 'GBH'),
('MAT-003', 'Multímetro', 'Instrumentación', 'Fluke', '117'),
('MAT-004', 'Router', 'Redes', 'Cisco', 'ISR'),
('MAT-005', 'Tablet', 'Movilidad', 'Samsung', 'Tab S');

-- ==========================================
-- MOVIMIENTOS HISTÓRICOS
-- ==========================================

-- Material 1: almacén -> Juan

INSERT INTO movimiento_material (
    id_material,
    id_ubicacion_origen,
    id_usuario_destino,
    tipo_movimiento,
    observaciones
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-001'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Almacén Central'),
    (SELECT id_usuario FROM usuario WHERE email='juan@empresa.com'),
    'ASIGNACION',
    'Entrega inicial'
);

-- Material 2: almacén -> Pedro

INSERT INTO movimiento_material (
    id_material,
    id_ubicacion_origen,
    id_usuario_destino,
    tipo_movimiento,
    observaciones
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-002'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Almacén Central'),
    (SELECT id_usuario FROM usuario WHERE email='pedro@empresa.com'),
    'ASIGNACION',
    'Herramienta para proyecto'
);

-- Material 3: almacén -> oficina

INSERT INTO movimiento_material (
    id_material,
    id_ubicacion_origen,
    id_ubicacion_destino,
    tipo_movimiento
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-003'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Almacén Central'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Oficina Técnica'),
    'TRASLADO'
);

-- Material 4: almacén -> taller

INSERT INTO movimiento_material (
    id_material,
    id_ubicacion_origen,
    id_ubicacion_destino,
    tipo_movimiento
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-004'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Almacén Central'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Taller'),
    'REPARACION'
);

-- Material 5: almacén -> María

INSERT INTO movimiento_material (
    id_material,
    id_ubicacion_origen,
    id_usuario_destino,
    tipo_movimiento
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-005'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Almacén Central'),
    (SELECT id_usuario FROM usuario WHERE email='maria@empresa.com'),
    'ASIGNACION'
);

-- ==========================================
-- DEVOLUCIÓN DE MATERIAL 1
-- ==========================================

INSERT INTO movimiento_material (
    id_material,
    id_usuario_origen,
    id_ubicacion_destino,
    tipo_movimiento,
    observaciones
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-001'),
    (SELECT id_usuario FROM usuario WHERE email='juan@empresa.com'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Almacén Central'),
    'DEVOLUCION',
    'Devolución al almacén'
);

-- ==========================================
-- ESTADO ACTUAL
-- ==========================================

-- MAT-001 devuelto al almacén

INSERT INTO estado_actual_material (
    id_material,
    id_usuario_actual,
    id_ubicacion_actual,
    id_estado
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-001'),
    NULL,
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Almacén Central'),
    (SELECT id_estado FROM estado_material WHERE nombre='Disponible')
);

-- MAT-002 asignado a Pedro

INSERT INTO estado_actual_material (
    id_material,
    id_usuario_actual,
    id_ubicacion_actual,
    id_estado
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-002'),
    (SELECT id_usuario FROM usuario WHERE email='pedro@empresa.com'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Oficina Técnica'),
    (SELECT id_estado FROM estado_material WHERE nombre='Asignado')
);

-- MAT-003 oficina

INSERT INTO estado_actual_material (
    id_material,
    id_ubicacion_actual,
    id_estado
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-003'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Oficina Técnica'),
    (SELECT id_estado FROM estado_material WHERE nombre='Disponible')
);

-- MAT-004 reparación

INSERT INTO estado_actual_material (
    id_material,
    id_ubicacion_actual,
    id_estado
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-004'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Taller'),
    (SELECT id_estado FROM estado_material WHERE nombre='En reparación')
);

-- MAT-005 asignado a María

INSERT INTO estado_actual_material (
    id_material,
    id_usuario_actual,
    id_ubicacion_actual,
    id_estado
)
VALUES (
    (SELECT id_material FROM material WHERE codigo='MAT-005'),
    (SELECT id_usuario FROM usuario WHERE email='maria@empresa.com'),
    (SELECT id_ubicacion FROM ubicacion WHERE nombre='Oficina Técnica'),
    (SELECT id_estado FROM estado_material WHERE nombre='Asignado')
);

-- ==========================================
-- VALIDACIÓN DE LA DEVOLUCIÓN DE MAT-001
-- ==========================================

INSERT INTO validacion_devolucion (
    id_movimiento,
    id_validador,
    resultado,
    observaciones
)
VALUES (
    (
        SELECT MAX(id_movimiento)
        FROM movimiento_material
        WHERE tipo_movimiento='DEVOLUCION'
    ),
    (SELECT id_usuario FROM usuario WHERE email='ana@empresa.com'),
    'APROBADA',
    'Material revisado correctamente'
);