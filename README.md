# Sistema de Gestión y Trazabilidad de Materiales

Sistema web en PHP y MySQL para la gestión de inventario de materiales con trazabilidad completa de movimientos, estados, ubicaciones y responsables.

El sistema permite conocer en todo momento dónde está cada material, quién lo tiene, su historial de movimientos y su estado actual.

---

## Stack

PHP (PDO)
MySQL
HTML
CSS
XAMPP (entorno de desarrollo)

---

## Requisitos

PHP 7.4 o superior
MySQL 5.7 o superior
XAMPP o servidor equivalente
Navegador web

---

## Instalación

Clonar repositorio:

git clone <URL_DEL_REPOSITORIO>

Colocar el proyecto en el directorio del servidor:

C:\xampp\htdocs\inventario

Iniciar Apache y MySQL desde XAMPP.

---

## Base de datos

Crear base de datos:

CREATE DATABASE inventario;
USE inventario;

Importar el schema ubicado en:

database.sql para la creación de la db

Después importar:
script.sql para rellenar tuplas de ejemplo

Desde terminal o phpMyAdmin.

---

## Ejecución

Abrir en navegador:

http://localhost/inventariov8/index.php

---

## Estructura del proyecto

/db.php
/index.php
/material.php
/movimientos.php
/nuevo_material.php
/baja_material.php
/nuevo_movimiento.php
/database.sql
/script.sql

/api
    buscar_materiales.php

/includes
    header.php
    footer.php

/css
    estilos.css

---

## Módulos principales

### Materiales
Creación de materiales con código automático basado en ID interno.
Baja lógica sin eliminación de historial.

### Movimientos
Registro completo de cambios de ubicación, usuario y estado.
Tipos de movimiento: asignación, traslado, devolución, reparación.

### Estado actual
Tabla de estado en tiempo real del material.
Incluye ubicación, responsable y estado.

### Historial
Historial completo por material y global.
Incluye origen, destino, tipo y observaciones.

---

## Reglas del sistema

Todo material debe tener una ubicación registrada.

No se elimina información histórica.

La baja de material es lógica mediante campo activo.

El código visible del material (MAT-XXX) se genera a partir del ID interno.

---

## Base de diseño

Sistema basado en trazabilidad completa:

movimiento_material almacena historial completo.

estado_actual_material representa el estado actual del sistema.

validacion_devolucion permite validaciones opcionales sin bloquear procesos.

