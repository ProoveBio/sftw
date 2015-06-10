<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150610183258 extends SchemaChange
{

	public function up()
	{
		$sql1 = <<< EOT
DROP TABLE IF EXISTS `tblSurveyField`;

CREATE TABLE `tblSurveyField`(
  `survey_field_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `survey_type_id` int(11) UNSIGNED DEFAULT NULL,
  `field_name` VARCHAR(200) DEFAULT NULL,
  `field_display_name` TEXT DEFAULT NULL,
  `is_deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`survey_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT;

		$sql2 = <<< EOT
INSERT INTO `tblSurveyType` 
	(`survey_type_name`,`survey_type_description`)
VALUES
	('ORT','Opioid Replacement Therapy data');
EOT;

		$sql3 = <<< EOT
INSERT INTO `tblSurveyField` 
	(`survey_type_id`,`field_name`,`field_display_name`)
SELECT `survey_type_id`, `custom_name`, `custom_display_name` FROM `tblTestTypeCustom` LEFT OUTER JOIN `tblSurveyType` ON `survey_type_name` = 'ORT' WHERE `test_type_id` = 3;
EOT;

		$sql4 = <<< EOT
DELETE FROM `tblTestTypeCustom` WHERE `test_type_id` = 3;
EOT;

		$this->querySQL($sql1);	
		$this->querySQL($sql2);	
		$this->querySQL($sql3);	
		$this->querySQL($sql4);	
	}

	public function down()
	{
		$sql1 = <<< EOT
INSERT INTO `tblTestTypeCustom` (`test_type_custom_id`, `test_type_id`, `custom_display_name`, `custom_name`, `type`, `order`)
VALUES
	(7,3,'Personal history of alcoholism','alcoholism','checkbox',1),
	(8,3,'Personal history of illegal drug abuse','drugAbuse','checkbox',2),
	(9,3,'Personal history of prescription drug abuse','presDrug','checkbox',3),
	(10,3,'Age between 16 and 45 old','age','checkbox',4),
	(11,3,'Mental health disorders\n(e.g. Anxiety, Attention Deficit Disorder,\nObsessive Compulsive Disorder,\nBipolar Disorder, Schizophrenia)','mentalDisorders','checkbox',5),
	(12,3,'Depression','depression','checkbox',6);
EOT;

		$sql2 = <<< EOT
DELETE FROM `tblSurveyType` WHERE `survey_type_name` = 'ORT';
ALTER TABLE `tblSurveyType` AUTO_INCREMENT = 1;
EOT;
		$sql3 = <<< EOT
DROP TABLE `tblSurveyField`;
EOT;

		$this->querySQL($sql1);
		$this->querySQL($sql2);
		$this->querySQL($sql3);
	}

}
