-- ─────────────────────────────────────────────────────────────────────────────
-- Tabla: prop_propietarios_adicionales
-- Propósito: almacenar los propietarios adicionales (2º, 3º, etc.) de un
--            inmueble registrado en `proprieter`. El propietario principal
--            sigue viviendo en las columnas de `proprieter`.
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `prop_propietarios_adicionales` (
  `id`                  INT(11)        NOT NULL AUTO_INCREMENT,
  `codigo_inmueble`     INT(11)        NOT NULL           COMMENT 'FK → proprieter.codigo',
  `doc_propietario`     VARCHAR(255)   NOT NULL           COMMENT 'CC / NIT del propietario',
  `nombre_propietario`  VARCHAR(255)   NOT NULL,
  `telefono_propietario` VARCHAR(50)   NOT NULL DEFAULT '',
  `email_propietario`   VARCHAR(255)   NOT NULL DEFAULT '',
  `banco`               VARCHAR(100)   NOT NULL DEFAULT '',
  `tipoCuenta`          VARCHAR(50)    NOT NULL DEFAULT '',
  `numeroCuenta`        VARCHAR(30)    NOT NULL DEFAULT '',
  `diaPago`             INT(11)        NOT NULL DEFAULT 0,
  `porcentaje`          DECIMAL(5,2)   NOT NULL DEFAULT 0.00 COMMENT '% de participación en el inmueble',
  `fecha_registro`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_codigo_inmueble` (`codigo_inmueble`),
  CONSTRAINT `fk_prop_adic_inmueble`
    FOREIGN KEY (`codigo_inmueble`)
    REFERENCES `proprieter` (`codigo`)
    
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci
  COMMENT='Propietarios adicionales por inmueble (el principal va en proprieter)';
