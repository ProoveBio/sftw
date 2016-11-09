<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161103210125 extends SchemaChange
{

	public function up()
	{
		$painFieldSQL = 'SELECT tblSurveyField.field_name,tblSurveyField.survey_type_id FROM tblSurveyField 
						INNER JOIN tblSurveyType ON tblSurveyType.survey_type_id = tblSurveyField.survey_type_id 
						WHERE survey_type_name = "PAIN"';
		$painFields = array();
		$surveyType = 4;
		foreach ($this->pdo->query($painFieldSQL) as $row){
			$painFields[] = $row['field_name'];
			$surveyType = $row['survey_type_id'];
		}

		$insertRows = array();
		$customSQL = 'SELECT encounter_id,test_type_custom_data FROM tblTest INNER JOIN tblTestType ON tblTest.test_type_id = tblTestType.test_type_id WHERE test_type_custom_data > "" AND external_test_type_id = "PBIO24" AND tblTest.is_deleted = 0'; 
		foreach ($this->pdo->query($customSQL) as $row){
			$painData = array();
			foreach ($painFields as $field){
				if ($field == "otherPain" || $field == "fibroDiagnosis"){
					$painData[$field] = null;
				} else {
					$painData[$field] = false;
				}
			}
			$customData = json_decode($row['test_type_custom_data']);
			foreach ($customData as $datum){
				if (isset($painData[$datum])){
					$painData[$datum] = true;
				}
			}

			$insertRows[] = "(" .$row['encounter_id']. "," . $surveyType . ",'" . json_encode($painData) . "')";
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
WHERE tblSurveyType.survey_type_name = "PAIN" AND tblSurvey.created_by = 0;

EOT;
		$this->querySQL($sql);
	}

}
