<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161128210041 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'UGT2B15' WHERE `custom_display_name` = 'UDP-Glucuronosyltransferase-2B15 (UGT2B15)';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CYP450 2B6' WHERE `custom_display_name` = 'Cytochrome P-450 2B6';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CYP450 2C8' WHERE `custom_display_name` = 'Cytochrome P-450 2C8';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CYP450 2C19' WHERE `custom_display_name` = 'Cytochrome P-450 2C19';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CYP450 3A4' WHERE `custom_display_name` = 'Cytochrome P-450 3A4';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'UGT2B7' WHERE `custom_display_name` = 'UDP-Glucuronosyltransferase-2B7 (UGT2B7)';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'VKORC1' WHERE `custom_display_name` = 'Vitamin K epoxide reductase complex subunit 1 (VKORC1)';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CYP450 1A2' WHERE `custom_display_name` = 'Cytochrome P-450 1A2';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CYP450 2C9' WHERE `custom_display_name` = 'Cytochrome P-450 2C9';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CYP450 2D6' WHERE `custom_display_name` = 'Cytochrome P-450 2D6';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CYP450 3A5' WHERE `custom_display_name` = 'Cytochrome P-450 3A5';
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'UDP-Glucuronosyltransferase-2B15 (UGT2B15)' WHERE `custom_display_name` = 'UGT2B15';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Cytochrome P-450 2B6' WHERE `custom_display_name` = 'CYP450 2B6';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Cytochrome P-450 2C8' WHERE `custom_display_name` = 'CYP450 2C8';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Cytochrome P-450 2C19' WHERE `custom_display_name` = 'CYP450 2C19';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Cytochrome P-450 3A4' WHERE `custom_display_name` = 'CYP450 3A4';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'UDP-Glucuronosyltransferase-2B7 (UGT2B7)' WHERE `custom_display_name` = 'UGT2B7';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Vitamin K epoxide reductase complex subunit 1 (VKORC1)' WHERE `custom_display_name` = 'VKORC1';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Cytochrome P-450 1A2' WHERE `custom_display_name` = 'CYP450 1A2';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Cytochrome P-450 2C9' WHERE `custom_display_name` = 'CYP450 2C9';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Cytochrome P-450 2D6' WHERE `custom_display_name` = 'CYP450 2D6';
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'Cytochrome P-450 3A5' WHERE `custom_display_name` = 'CYP450 3A5';
EOT;
		$this->querySQL($sql);
	}

}
