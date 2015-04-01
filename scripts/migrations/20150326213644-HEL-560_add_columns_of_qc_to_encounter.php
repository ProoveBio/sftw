<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150326213644 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblEncounter` 
    ADD `is_qced` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `patient_address_id`, 
    ADD `qced_by` INT(10) UNSIGNED NULL AFTER `is_qced`, 
    ADD `qced_at` DATETIME NULL AFTER `qced_by`;

ALTER TABLE `tblEncounterHistory` 
    ADD `is_qced` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `patient_address_id`, 
    ADD `qced_by` INT(10) UNSIGNED NULL AFTER `is_qced`, 
    ADD `qced_at` DATETIME NULL AFTER `qced_by`;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblEncounter`
  DROP `is_qced`,
  DROP `qced_by`,
  DROP `qced_at`;

ALTER TABLE `tblEncounterHistory`
  DROP `is_qced`,
  DROP `qced_by`,
  DROP `qced_at`;
EOT;
		$this->querySQL($sql);
	}

}
