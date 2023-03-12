CREATE DATABASE npz;
USE npz;

-- 1. Инициализация входных данных
DROP TABLE IF EXISTS factory_data;

CREATE TABLE factory_data (
  `id` VARCHAR(255),
  `full_name` VARCHAR(255),
  `short_name` VARCHAR(255),
  `legal_address` VARCHAR(255),
  `actual_address` VARCHAR(255),
  `processing_depth` VARCHAR(255),
  `register_info` VARCHAR(255),
  `status` VARCHAR(255),
  PRIMARY KEY (`id`));

SET GLOBAL local_infile = 1;
LOAD DATA LOCAL INFILE
 'C:/SQL/factory.csv'
 INTO TABLE factory_data
 FIELDS TERMINATED BY ',' ENCLOSED BY '"'
 LINES TERMINATED BY '\r\n'
 IGNORE 2 LINES;

 -- 2. Заполнение таблиц
 
DROP TABLE IF EXISTS status; 
 
CREATE TABLE status (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status_text` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));
  
INSERT INTO status (status_text)
SELECT DISTINCT
  status
FROM factory_data;

DROP TABLE IF EXISTS factory;

CREATE TABLE factory (
  `id` INT,
  `full_name` VARCHAR(255) NULL,
  `short_name` VARCHAR(255) NULL,
  `legal_address` VARCHAR(255) NULL,
  `actual_address` VARCHAR(255) NULL,
  `processing_depth` VARCHAR(255) NULL,
  `register_info`  VARCHAR(255) NULL,
  `status_id` INT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY factory_fk1 (status_id) REFERENCES status (id)
  );
  
INSERT INTO factory (id, full_name, short_name, legal_address, actual_address, processing_depth, register_info, status_id)
SELECT DISTINCT
  d.id,
  d.full_name,
  d.short_name,
  d.legal_address,
  d.actual_address,
  d.processing_depth,
  d.register_info,
  s.id
 FROM factory_data d
  LEFT JOIN status s ON d.status = s.status_text;
  
  CREATE TABLE IF NOT EXISTS production (
  `factory_id` INT NOT NULL,
  `production` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`factory_id`, `production`),
  FOREIGN KEY factory_fk2 (factory_id) REFERENCES factory (id)
  );
  
LOAD DATA LOCAL INFILE
 'C:/SQL/factory_products.csv'
 INTO TABLE production
 FIELDS TERMINATED BY ',' ENCLOSED BY '"'
 LINES TERMINATED BY '\r\n'
 IGNORE 2 LINES;
 
 
