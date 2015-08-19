<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150819231240 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
INSERT INTO `tblSurveyField` 
    (`survey_field_id`, `survey_type_id`, `field_name`, `field_display_name`, `is_deleted`) 
    VALUES 
    (NULL, '2', 'family-alcoholism', 'Family history of alcoholism', '0'), 
    (NULL, '2', 'family-drugAbuse', 'Family history of illegal drug abuse', '0'),
    (NULL, '2', 'family-presDrug', 'Family history of prescription drug abuse ', '0');
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DELETE FROM `tblSurveyField` WHERE `field_name` IN ('family-alcoholism', 'family-drugAbuse', 'family-presDrug');
EOT;
		$this->querySQL($sql);
	}

}
