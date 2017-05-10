<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170510195844 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblCorrectionCode` ADD `affected_tests` SET('PBIO1','PBIO2','PBIO3','PBIO4','PBIO5','PBIO6','PBIO7','PBIO8','PBIO9','PBIO10','PBIO11','PBIO12','PBIO13','PBIO14','PBIO15','PBIO16','PBIO17','PBIO18','PBIO19','PBIO20','PBIO21','PBIO22','PBIO23','PBIO24','PBIO25','PBIO26','PBIO27','PBIO28','PBIO29','PBIO30','PBIO31','PBIO32','PBIO33','PBIO34','PBIO35','SUMMARY2','PBIO2A','PBIO2B','PBIO2C') NULL DEFAULT NULL COMMENT 'Tests will be affected when dos_update set to true' AFTER `dos_update`;

SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE `tblCorrectionCode`;
INSERT INTO `tblCorrectionCode` (`correctioncode_id`, `code`, `description`, `dos_update`, `affected_tests`, `is_active`) VALUES
(1, 'Patient ID', 'Patient ID', 0, NULL, 1),
(2, 'Patient Name', 'Correction to the Patient''s Name', 0, NULL, 1),
(3, 'Patient DOB', 'Correction to Patient''s DOB', 1, 'PBIO1,PBIO27,PBIO28', 1),
(4, 'Patient Race', 'Correction to Patient''s Race', 1, 'PBIO4,PBIO25,PBIO27,PBIO28', 1),
(5, 'Patient Sex', 'Correction to Patient''s Sex', 1, 'PBIO4,PBIO27,PBIO28', 1),
(6, 'Physician Name', 'Correction to Ordering Physician', 0, NULL, 1),
(7, 'Account Name', 'Correction to Account Name', 0, NULL, 1),
(8, 'Date of Service', 'Correction to the Original DOS', 0, NULL, 1),
(9, 'Date Received', 'Correction to "Date Received by Lab"', 0, NULL, 1),
(10, 'Date of Injury', 'Correction to "Date of Injury"', 0, NULL, 1),
(11, 'Stress Questions', 'Correction to Patient''s responses to Stress Questions', 1, 'PBIO4,PBIO26', 1),
(12, 'History Question', 'Correction to Patient''s responses to Persona''/Family History Questions ', 1, 'PBIO1,PBIO27', 1),
(13, 'Pain Points', 'Correction to Patient''s pain points', 1, 'PBIO24,PBIO26', 1),
(14, 'TMD Questions', 'Correction to Patient''s responses to TMD Questions ', 1, 'PBIO26', 1);
SET FOREIGN_KEY_CHECKS=1;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblCorrectionCode` DROP `affected_tests`;
EOT;
		$this->querySQL($sql);
	}

}
