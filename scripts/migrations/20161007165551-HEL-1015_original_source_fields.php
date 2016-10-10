<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161007165551 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT

ALTER TABLE `tblEncounter` ADD `original_source` VARCHAR(50) NOT NULL DEFAULT 'helix' AFTER  `account_id`;
ALTER TABLE `tblEncounterHistory` ADD `original_source` VARCHAR(50) NOT NULL DEFAULT 'helix' AFTER  `account_id`;

ALTER TABLE `tblPatient`
	ADD `sf_account_id` VARCHAR(50) NULL DEFAULT NULL AFTER `external_patient_id`,
	ADD `original_source` VARCHAR(50) NOT NULL DEFAULT 'helix' AFTER `external_patient_id`;

ALTER TABLE `tblPatientHistory`
	ADD `sf_account_id` VARCHAR(50) NULL DEFAULT NULL AFTER `external_patient_id`,
	ADD `original_source` VARCHAR(50) NOT NULL DEFAULT 'helix' AFTER `external_patient_id`;

EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT

ALTER TABLE `tblEncounter` DROP COLUMN `original_source`;
ALTER TABLE `tblEncounterHistory` DROP COLUMN `original_source`;

ALTER TABLE `tblPatient`
	DROP COLUMN `sf_account_id`,
	DROP COLUMN `original_source`;

ALTER TABLE `tblPatientHistory`
	DROP COLUMN `sf_account_id`,
	DROP COLUMN `original_source`;

EOT;
		$this->querySQL($sql);
	}

}
