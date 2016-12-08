<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161208204705 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
UPDATE `tblSurveyField` SET `sort_order` = 9 WHERE `survey_type_id` = 2 AND `field_name` = 'mentalDisorders';
UPDATE `tblSurveyField` SET `sort_order` = 8 WHERE `survey_type_id` = 2 AND `field_name` = 'depression';
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
UPDATE `tblSurveyField` SET `sort_order` = 8 WHERE `survey_type_id` = 2 AND `field_name` = 'mentalDisorders';
UPDATE `tblSurveyField` SET `sort_order` = 9 WHERE `survey_type_id` = 2 AND `field_name` = 'depression';
EOT;
		$this->querySQL($sql);
	}

}
