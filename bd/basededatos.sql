SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table TiposUsuarios
-- -----------------------------------------------------
DROP TABLE IF EXISTS TiposUsuarios;

CREATE TABLE IF NOT EXISTS TiposUsuarios (
  idTipoUsuarios INT NOT NULL,
  codigoTipoUsuarios INT NOT NULL,
  descripcionTipoUsuarios VARCHAR(45) NOT NULL,
  PRIMARY KEY ('idTipoUsuarios'),
  UNIQUE INDEX codigoUsuarios_UNIQUE (codigoTipoUsuarios),
  UNIQUE INDEX descripcionUsuarios_UNIQUE (descripcionTipoUsuarios));

-- -----------------------------------------------------
-- Table Estados
-- -----------------------------------------------------
DROP TABLE IF EXISTS Estados;

CREATE TABLE IF NOT EXISTS Estados (
  idEstados INT NULL,
  codigoEstado INT NOT NULL,
  descripcionEstado VARCHAR(45) NOT NULL,
  UNIQUE INDEX codigoEstado_UNIQUE (codigoEstado),
  PRIMARY KEY (idEstados));


-- -----------------------------------------------------
-- Table TiposSubasta
-- -----------------------------------------------------
DROP TABLE IF EXISTS TiposSubasta;

CREATE TABLE IF NOT EXISTS TiposSubasta (
  idtiposSubasta INT NOT NULL,
  codigoSubasta INT NOT NULL,
  DescripcionSubasta VARCHAR(80) NOT NULL,
  UNIQUE INDEX codigoSubasta_UNIQUE (codigoSubasta),
  UNIQUE INDEX DescripcionSubasta_UNIQUE (DescripcionSubasta ASC),
  PRIMARY KEY (idtiposSubasta));


-- -----------------------------------------------------
-- Table Subasta
-- -----------------------------------------------------
DROP TABLE IF EXISTS Subasta ;

CREATE TABLE IF NOT EXISTS Subasta (
  idSubasta INT NOT NULL,
  fecha_inicio DATETIME NOT NULL,
  fecha_fin DATETIME NOT NULL,
  fecha_roundRobin DATETIME NULL,
  precio DOUBLE NOT NULL,
  idEstado INT NOT NULL,
  idTipoSubasta INT NOT NULL,
  PRIMARY KEY (idSubasta),
  INDEX idEstado_idx (idEstado ASC),
  INDEX idTiposSubasta_idx (idTipoSubasta ASC),
  CONSTRAINT idEstado FOREIGN KEY (idEstado) REFERENCES Estados (idEstados),
  CONSTRAINT idTiposSubasta FOREIGN KEY (idTipoSubasta) REFERENCES TiposSubasta (idtiposSubasta) ON DELETE NO ACTION ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table Usuarios
-- -----------------------------------------------------
DROP TABLE IF EXISTS Usuarios;

CREATE TABLE IF NOT EXISTS Usuarios(
  idUsuarios INT NOT NULL,
  Nombre VARCHAR(45) NOT NULL,
  Password VARCHAR(60) NOT NULL,
  idTipoUsuarios INT NOT NULL,
  PRIMARY KEY (idUsuarios),
  UNIQUE INDEX Nombre_UNIQUE (Nombre ASC),
  INDEX idTipoUsuarios_idx (idTipoUsuarios ASC),
  INDEX idSubasta_idx (idSubasta ASC),
  CONSTRAINT idTipoUsuarios FOREIGN KEY (idTipoUsuarios) REFERENCES TiposUsuarios (idTipoUsuarios) ON DELETE NO ACTION ON UPDATE NO ACTION,
  );

-- -----------------------------------------------------
-- Table Relación entre el usuario y sus subastas
-- -----------------------------------------------------
DROP TABLE IF EXISTS Usuario_Subasta;

CREATE TABLE IF NOT EXISTS Usuario_SUbasta ( 
  idUsuarios INT NOT NULL,
  idSUbasta INT NOT NULL,
  INDEX idUsuario_idx (idUsuarios ASC),
  INDEX idSubasta_idx (idSubasta ASC),
  CONSTRAINT idUsuario_SUbasta1 FOREIGN KEY (idUsuarios) REFERENCES Usuarios (idUsuarios) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT idUsuario_Subasta2 FOREIGN KEY (idSubasta) REFERENCES Subasta (idSubasta) ON DELETE NO ACTION ON UPDATE NO ACTION
);

-- -----------------------------------------------------
-- Table Productos
-- -----------------------------------------------------
DROP TABLE IF EXISTS Productos;

CREATE TABLE IF NOT EXISTS Productos (
  idProductos INT NOT NULL,
  precio_inicio DOUBLE NOT NULL,
  concepto VARCHAR(20) NOT NULL,
  descripcion VARCHAR(100) NULL DEFAULT 'Sin Información concreta.',
  imagen BLOB NULL,
  fecha DATETIME NULL,
  idEstado INT NULL,
  PRIMARY KEY (idProductos),
  INDEX idEstado_idx (idEstado ASC),
  CONSTRAINT idEstado_Productos FOREIGN KEY (idEstado) REFERENCES Estados (idEstados) ON DELETE NO ACTION ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table Log
-- -----------------------------------------------------
DROP TABLE IF EXISTS Log;

CREATE TABLE IF NOT EXISTS Log(
  idLog INT NOT NULL,
  Fecha DATETIME NOT NULL,
  Descripcion VARCHAR(145) NOT NULL,
  idUsuario INT NOT NULL COMMENT 'segun el idTipoUsuario se definiría como subastador o postor' NOT NULL,
  idSubasta INT,
  idProducto INT,
  PRIMARY KEY (idLog)
  INDEX idSubasta_idx (idSubasta ASC),
  INDEX idUsuario_idx (idUsuario ASC),
  INDEX idProducto_idx (idProducto ASC),
  CONSTRAINT idSubasta_Log FOREIGN KEY (idSubasta) REFERENCES Subasta (idSubasta),
  CONSTRAINT idUsuario_Log FOREIGN KEY (idUsuario) REFERENCES Usuarios (idUsuarios),
  CONSTRAINT idProducto_Log FOREIGN KEY (idProducto) REFERENCES Productos (idProducto)
  );



-- -----------------------------------------------------
-- Table Pujas 
-- -----------------------------------------------------
DROP TABLE IF EXISTS Pujas;

CREATE TABLE IF NOT EXISTS Pujas (
  idPujas INT NOT NULL,
  Cantidad FLOAT NOT NULL,
  idUsuario INT NOT NULL,
  idSubasta INT NOT NULL,
  fecha DATETIME NULL,
  PRIMARY KEY (idPujas),
  INDEX idUsuario_idx (idUsuario ASC),
  INDEX idSubasta_idx (idSubasta ASC),
  CONSTRAINT idUsuario_Pujas FOREIGN KEY (idUsuario) REFERENCES Usuarios (idUsuarios),
  CONSTRAINT idSubasta_Pujas FOREIGN KEY (idSubasta) REFERENCES Subasta (idSubasta));



-- -----------------------------------------------------
-- Table Lotes error
-- -----------------------------------------------------
DROP TABLE IF EXISTS Lotes ;

CREATE TABLE IF NOT EXISTS Lotes(
  idProducto INT NOT NULL COMMENT 'Relacion entre producto y subasta',
  idSubasta INT NOT NULL,
  UNIQUE INDEX idProducto_UNIQUE (idProducto ASC),
  UNIQUE INDEX idSubasta_UNIQUE (idSubasta ASC),
  CONSTRAINT idProducto_Lotes  FOREIGN KEY (idProducto) REFERENCES Productos (idProductos),
  CONSTRAINT idSubasta_Lotes   FOREIGN KEY (idSubasta)  REFERENCES Subasta (idSubasta));
