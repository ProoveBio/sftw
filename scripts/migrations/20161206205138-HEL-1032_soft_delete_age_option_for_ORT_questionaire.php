<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161206205138 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
UPDATE `tblSurveyField` SET `is_deleted` = '1' WHERE `survey_type_id` = 2 AND `field_name` = 'age'; 
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
UPDATE `tblSurveyField` SET `is_deleted` = '0' WHERE `survey_type_id` = 2 AND `field_name` = 'age';
EOT;
		$this->querySQL($sql);
	}

}
