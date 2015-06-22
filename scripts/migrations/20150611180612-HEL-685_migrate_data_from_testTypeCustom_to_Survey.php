<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150611180612 extends SchemaChange
{

	public function up()
	{
		$ortFieldSQL = 'SELECT tblSurveyField.field_name,tblSurveyField.survey_type_id FROM tblSurveyField 
						INNER JOIN tblSurveyType ON tblSurveyType.survey_type_id = tblSurveyField.survey_type_id 
						WHERE survey_type_name = "ORT"';
		$ortFields = array();
		$ortType = 2;
		foreach ($this->pdo->query($ortFieldSQL) as $row){
			$ortFields[] = $row['field_name'];
			$ortType = $row['survey_type_id'];
		}

		$insertRows = array();
		$customSQL = 'SELECT encounter_id,test_type_custom_data FROM tblTest WHERE test_type_custom_data > "" AND test_type_id IN (2,3) AND is_deleted = 0'; 
		foreach ($this->pdo->query($customSQL) as $row){
			$ortData = array();
			foreach ($ortFields as $field){
				$ortData[$field] = 2;
			}
			$customData = json_decode($row['test_type_custom_data']);
			foreach ($customData as $datum){
				if (isset($ortData[$datum])){
					$ortData[$datum] = 1;
				}
			}

			$insertRows[] = "(" .$row['encounter_id']. "," . $ortType . ",'" . json_encode($ortData) . "')";
		}		
		if (count($insertRows)){
			$migrateSQL = 'INSERT INTO `tblSurvey` (`encounter_id`, `survey_type_id`, `survey_json`) VALUES ';
			$migrateSQL .= implode(',',$insertRows);

			$this->querySQL($migrateSQL);	
		}
	}

	public function down()
	{
		$sql = <<< EOT
DELETE tblSurvey FROM tblSurvey 
INNER JOIN tblSurveyType ON tblSurvey.survey_type_id = tblSurveyType.survey_type_id 
WHERE tblSurveyType.survey_type_name = "ORT" AND tblSurvey.created_by = 0;
EOT;
		$this->querySQL($sql);
	}

}
