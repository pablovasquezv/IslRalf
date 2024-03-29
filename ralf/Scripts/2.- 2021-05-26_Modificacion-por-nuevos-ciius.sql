
-- Se aumentan el max de caracteres para almacenar el ciiu traido del SPM
ALTER TABLE ralf.empleador MODIFY COLUMN ciiu_empleador varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Código de actividad del empleador.';
