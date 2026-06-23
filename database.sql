-- =========================================
-- CREACIÓN DE BASE DE DATOS
-- =========================================

CREATE DATABASE IF NOT EXISTS inventario
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE inventario;

-- =========================================
-- TABLA: MATERIAL
-- =========================================

CREATE TABLE material (
    id_material INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE,
    descripcion TEXT,
    categoria VARCHAR(100),
    marca VARCHAR(100),
    modelo VARCHAR(100),
    fecha_alta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
);

-- =========================================
-- TABLA: USUARIO
-- =========================================

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    cargo VARCHAR(100),
    activo TINYINT(1) DEFAULT 1
);

-- =========================================
-- TABLA: UBICACION
-- =========================================

CREATE TABLE ubicacion (
    id_ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo VARCHAR(100)
);

-- =========================================
-- TABLA: ESTADO MATERIAL
-- =========================================

CREATE TABLE estado_material (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- =========================================
-- TABLA: MOVIMIENTO MATERIAL
-- =========================================

CREATE TABLE movimiento_material (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_material INT NOT NULL,
    id_usuario_origen INT NULL,
    id_usuario_destino INT NULL,
    id_ubicacion_origen INT NULL,
    id_ubicacion_destino INT NULL,
    tipo_movimiento VARCHAR(100),
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,

    FOREIGN KEY (id_material) REFERENCES material(id_material),
    FOREIGN KEY (id_usuario_origen) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_usuario_destino) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_ubicacion_origen) REFERENCES ubicacion(id_ubicacion),
    FOREIGN KEY (id_ubicacion_destino) REFERENCES ubicacion(id_ubicacion)
);

-- =========================================
-- TABLA: ESTADO ACTUAL MATERIAL
-- =========================================

CREATE TABLE estado_actual_material (
    id_material INT PRIMARY KEY,

    id_usuario_actual INT NULL,

    id_ubicacion_actual INT NOT NULL,

    id_estado INT NOT NULL,

    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_material)
        REFERENCES material(id_material)
        ON DELETE CASCADE,

    FOREIGN KEY (id_usuario_actual)
        REFERENCES usuario(id_usuario),

    FOREIGN KEY (id_ubicacion_actual)
        REFERENCES ubicacion(id_ubicacion),

    FOREIGN KEY (id_estado)
        REFERENCES estado_material(id_estado)
);
-- =========================================
-- TABLA: VALIDACION DEVOLUCION
-- =========================================

CREATE TABLE validacion_devolucion (
    id_validacion INT AUTO_INCREMENT PRIMARY KEY,
    id_movimiento INT NOT NULL,
    id_validador INT NOT NULL,
    fecha_validacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resultado VARCHAR(50),
    observaciones TEXT,

    FOREIGN KEY (id_movimiento) REFERENCES movimiento_material(id_movimiento),
    FOREIGN KEY (id_validador) REFERENCES usuario(id_usuario)
);