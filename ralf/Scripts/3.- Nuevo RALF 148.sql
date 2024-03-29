--  Nuevo RALF 148
INSERT INTO tipo_xml (TPXML_ID,NOMBRE,DESCRIPCION,XSD)
	VALUES (148,'RALF Recargo Tasa','(RALF) Recargo Tasa','SISESAT_RALF_Recargo_tasa.1.0.xsd');
	
-- NUEVA TABLA
CREATE TABLE ralfRecargoTasa (
	id_recargoTasa int(11) auto_increment NOT NULL,
	xml_id int(11) NULL,
	nro_resolucion varchar(100) NULL,
	fecha_resolucion DATE NULL,
	causal_recargo int(11) NULL,
	proceso_asociado_recargo int(11) NULL,
	otro_proceso_asociado_recargo varchar(250) NULL,
	nro_total_trabajadores int(11) NULL,
	magnitud_incumplimiento decimal(4,0) NULL,
	porcentaje_recargo int(11) NULL,
	tase_adicional_110 decimal(4,0) NULL,
	recargo_resultante decimal(4,0) NULL,
	tasa_cot_adicional decimal(4,0) NULL,
	tasa_adicional_recargo decimal(4,0) NULL,
	vigencia DATE NULL,
	centro_trabajo LONGTEXT NULL,
	CONSTRAINT ralfRecargoTasa_PK PRIMARY KEY (id_recargoTasa),
	CONSTRAINT ralfRecargoTasa_FK FOREIGN KEY (xml_id) REFERENCES xml(XML_ID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;
CREATE INDEX ralfRecargoTasa_id_recargoTasa_IDX USING BTREE ON ralfRecargoTasa (id_recargoTasa,xml_id);

-- ME falto este campo
ALTER TABLE ralfRecargoTasa ADD tipo_resolucion_informada int(12) NULL;
ALTER TABLE ralfRecargoTasa CHANGE tipo_resolucion_informada tipo_resolucion_informada int(12) NULL AFTER xml_id;
ALTER TABLE ralfRecargoTasa CHANGE porcentaje_recargo porcentaje_base_recargo int(11) NULL;
ALTER TABLE ralfRecargoTasa ADD porcentaje_recargo decimal(4,0) NULL;
ALTER TABLE ralfRecargoTasa CHANGE porcentaje_recargo porcentaje_recargo decimal(4,0) NULL AFTER porcentaje_base_recargo;
