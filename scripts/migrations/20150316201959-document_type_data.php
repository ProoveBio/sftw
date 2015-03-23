<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150316201959 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
TRUNCATE TABLE tblDocumentType;

ALTER TABLE tblDocumentType
	MODIFY document_type_id SMALLINT(6) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO tblDocumentType
	(type_description,type_name)
VALUES
	("Testing Agreement","TA"),
	("Request for Authorization","RFA"),
	("Medical Necessity Letter","MN"),
	("Informed Consent","IC"),
	("Survey","SUR"),
	("Patient Demographics","DEMO"),
	("Lab Directed Treatment Plan","LDTP"),
	("Medical Regiment","MR"),
	("Investigator Intervention Evaluation","IIE"),
	("Other","MISC");

EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
TRUNCATE TABLE tblDocumentType;

INSERT INTO tblDocumentType
	(type_description,type_name)
VALUES
	("Testing Agreement","TA"),
	("Request for Authorization","RFA"),
	("Medical Necessity","MN"),
	("Informed Consent","IC"),
	("Miscellaneous document","MIS"),
	("Survey","SUR"),
	("Demographic","DEM");
EOT;
		$this->querySQL($sql);
	}

}
