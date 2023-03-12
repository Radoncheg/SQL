USE lms_backend;

CREATE TABLE course
(
  course_id VARCHAR(36),
  version INT UNSIGNED,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (course_id)
);

CREATE TABLE course_material
(
  module_id VARCHAR(36),
  course_id VARCHAR(36),
  is_required BOOLEAN,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (module_id),
  FOREIGN KEY fk_course_id_key (course_id)
    REFERENCES course (course_id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE course_enrollment
(
  enrollment_id VARCHAR(36),
  course_id VARCHAR(36),
  PRIMARY KEY (enrollment_id),
  FOREIGN KEY fk_course_id_key (course_id)
    REFERENCES course (course_id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE course_status
(
  enrollment_id VARCHAR(36),
  progress DECIMAL(3, 0),
  duration INT,
  PRIMARY KEY (enrollment_id),
  FOREIGN KEY fk_enrollment_id_key (enrollment_id)
    REFERENCES course_enrollment (enrollment_id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE course_module_status
(
  enrollment_id VARCHAR(36),
  module_id VARCHAR(36),
  progress DECIMAL(3, 0),
  duration INT,
  PRIMARY KEY (enrollment_id, module_id),
  FOREIGN KEY course_enrollment_id_fk (enrollment_id)
    REFERENCES course_enrollment (enrollment_id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY course_module_id_fk (module_id)
    REFERENCES course_material (module_id)
    ON UPDATE CASCADE ON DELETE CASCADE
);