<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161107204849 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT

UPDATE `tblTestType` SET `test_type_name` = "Proove TMD" WHERE `external_test_type_id` = "PBIO26";
UPDATE `tblTestType` SET `test_type_name` = "Proove Opioid Risk and Response Comprehensive" WHERE `external_test_type_id` = "PBIO27";
UPDATE `tblTestType` SET `test_type_name` = "Proove Opioid-induced Side Effects" WHERE `external_test_type_id` = "PBIO28";

UPDATE `tblTestType` SET `parent_test_id` = 30 WHERE `external_test_type_id` IN ("PBIO1","PBIO5","PBIO28");
UPDATE `tblTestType` SET `parent_test_id` = NULL WHERE `external_test_type_id` IN ("PBIO26","PBIO27");

UPDATE `tblTestType` SET `is_active` = 1 WHERE `external_test_type_id` IN ('PBIO23','PBIO26','PBIO27','PBIO28');

INSERT INTO `tblTestTypeCustom` VALUES (47,5,"Neurologics","neuro","checkbox",4);

EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT

UPDATE `tblTestType` SET `test_type_name` = "Proove Migraine Risk" WHERE `external_test_type_id` = "PBIO26";
UPDATE `tblTestType` SET `test_type_name` = "Proove ADHD" WHERE `external_test_type_id` = "PBIO27";
UPDATE `tblTestType` SET `test_type_name` = "Proove Bipolar Disorder Profile" WHERE `external_test_type_id` = "PBIO28";

UPDATE `tblTestType` SET `parent_test_id` = NULL WHERE `external_test_type_id` IN ("PBIO1","PBIO5");
UPDATE `tblTestType` SET `parent_test_id` = 25 WHERE `external_test_type_id` IN ("PBIO26","PBIO27","PBIO28");

UPDATE `tblTestType` SET `is_active` = 0 WHERE `external_test_type_id` IN ('PBIO23','PBIO26','PBIO27','PBIO28');

DELETE FROM `tblTestTypeCustom` WHERE `test_type_custom_id` = 47;

EOT;
		$this->querySQL($sql);
	}

}
