CREATE USER 'lms-backend-app'@'%' IDENTIFIED BY 'kUUTyU7LssSc';

CREATE DATABASE lms_backend;
GRANT ALL ON lms_backend.* TO 'lms-backend-app'@'%';
