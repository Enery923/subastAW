SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS 'mydb' ;

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS 'mydb' DEFAULT CHARACTER SET utf8 ;
USE 'mydb' ;

-- -----------------------------------------------------
-- Table 'mydb'.'TiposUsuarios'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'TiposUsuarios' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'TiposUsuarios' (
  'idTipoUsuarios' INT NOT NULL,
  'codigoTipoUsuarios' INT NOT NULL,
  'descripcionTipoUsuarios' VARCHAR(45) NOT NULL,
  PRIMARY KEY ('idTipoUsuarios'),
  UNIQUE INDEX 'codigoUsuarios_UNIQUE' ('codigoTipoUsuarios' ASC),
  UNIQUE INDEX 'descripcionUsuarios_UNIQUE' ('descripcionTipoUsuarios' ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'mydb'.'Estados'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'Estados' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'Estados' (
  'idEstados' INT NULL,
  'codigoEstado' INT NOT NULL,
  'descripcionEstado' VARCHAR(45) NOT NULL,
  UNIQUE INDEX 'codigoEstado_UNIQUE' ('codigoEstado' ASC),
  PRIMARY KEY ('idEstados`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'mydb'.'TiposSubasta'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'TiposSubasta' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'TiposSubasta' (
  'idtiposSubasta' INT NOT NULL,
  'codigoSubasta' INT NOT NULL,
  'DescripcionSubasta' VARCHAR(80) NOT NULL,
  UNIQUE INDEX 'codigoSubasta_UNIQUE' ('codigoSubasta' ASC),
  UNIQUE INDEX 'DescripcionSubasta_UNIQUE' ('DescripcionSubasta' ASC),
  PRIMARY KEY ('idtiposSubasta`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'mydb'.'Subasta'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'Subasta' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'Subasta' (
  'idSubasta' INT NOT NULL,
  'fecha_inicio' DATETIME NOT NULL,
  'fecha_fin' DATETIME NOT NULL,
  'fecha_roundRobin' DATETIME NULL,
  'precio' DOUBLE NOT NULL,
  'idEstado' INT NOT NULL,
  'idTipoSubasta' INT NOT NULL,
  PRIMARY KEY ('idSubasta'),
  INDEX 'idEstado_idx' ('idEstado' ASC),
  INDEX 'idTiposSubasta_idx' ('idTipoSubasta' ASC),
  CONSTRAINT 'idEstado'
    FOREIGN KEY ('idEstado')
    REFERENCES 'mydb'.'Estados' ('idEstados')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'idTiposSubasta'
    FOREIGN KEY ('idTipoSubasta')
    REFERENCES 'mydb'.'TiposSubasta' ('idtiposSubasta')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'mydb'.'Usuarios'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'Usuarios' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'Usuarios' (
  'idUsuarios' INT NOT NULL,
  'Nombre' VARCHAR(45) NOT NULL,
  'Contrasenia' VARCHAR(45) NOT NULL,
  'idTipoUsuarios' INT NOT NULL,
  'idSubasta' INT NOT NULL,
  PRIMARY KEY ('idUsuarios'),
  UNIQUE INDEX 'Nombre_UNIQUE' ('Nombre' ASC),
  INDEX 'idTipoUsuarios_idx' ('idTipoUsuarios' ASC),
  INDEX 'idSubasta_idx' ('idSubasta' ASC),
  CONSTRAINT 'idTipoUsuarios'
    FOREIGN KEY ('idTipoUsuarios')
    REFERENCES 'mydb'.'TiposUsuarios' ('idTipoUsuarios')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'idSubasta'
    FOREIGN KEY ('idSubasta')
    REFERENCES 'mydb'.'Subasta' ('idSubasta')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'mydb'.'Productos'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'Productos' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'Productos' (
  'idProductos' INT NOT NULL,
  'precio_inicio' DOUBLE NOT NULL,
  'concepto' VARCHAR(20) NOT NULL,
  'descripcion' VARCHAR(100) NULL DEFAULT 'Sin Información concreta.',
  'imagen' BLOB NULL,
  'fecha' DATETIME NULL,
  'idEstado' INT NULL,
  PRIMARY KEY ('idProductos'),
  INDEX 'idEstado_idx' ('idEstado' ASC),
  CONSTRAINT 'idEstado'
    FOREIGN KEY ('idEstado')
    REFERENCES 'mydb'.'Estados' ('idEstados')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'mydb'.'Log'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'Log' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'Log' (
  'idLog' INT NOT NULL,
  'Fecha' DATETIME NOT NULL,
  'Descripcion' VARCHAR(145) NOT NULL,
  'idVinculo' INT NOT NULL COMMENT 'Es la Foreign key que lo relaciona con idUsuario o idProducto',
  PRIMARY KEY ('idLog'))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'mydb'.'Pujas'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'Pujas' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'Pujas' (
  'idPujas' INT NOT NULL,
  'Cantidad' FLOAT NOT NULL,
  'idUsuario' INT NOT NULL,
  'idSubasta' INT NOT NULL,
  'fecha' DATETIME NULL,
  PRIMARY KEY ('idPujas'),
  INDEX 'idUsuario_idx' ('idUsuario' ASC),
  INDEX 'idSubasta_idx' ('idSubasta' ASC),
  CONSTRAINT 'idUsuario'
    FOREIGN KEY ('idUsuario')
    REFERENCES 'mydb'.'Usuarios' ('idUsuarios')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'idSubasta'
    FOREIGN KEY ('idSubasta')
    REFERENCES 'mydb'.'Subasta' ('idSubasta')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table 'mydb'.'Lotes'
-- -----------------------------------------------------
DROP TABLE IF EXISTS 'mydb'.'Lotes' ;

CREATE TABLE IF NOT EXISTS 'mydb'.'Lotes' (
  'idProducto' INT NOT NULL COMMENT 'Relacion entre producto y subasta',
  'idSubasta' INT NOT NULL,
  UNIQUE INDEX 'idProducto_UNIQUE' ('idProducto' ASC),
  UNIQUE INDEX 'idSubasta_UNIQUE' ('idSubasta' ASC),
  CONSTRAINT 'idProducto'
    FOREIGN KEY ('idProducto')
    REFERENCES 'mydb'.'Productos' ('idProductos')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT 'idSubasta'
    FOREIGN KEY ('idSubasta')
    REFERENCES 'mydb'.'Subasta' ('idSubasta')
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
