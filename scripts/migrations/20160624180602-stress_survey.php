<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20160624180602 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT


TRUNCATE TABLE `tblSurveyType`;
INSERT INTO `tblSurveyType` (`survey_type_id`, `survey_type_name`, `survey_type_description`)
VALUES
	(1,'DEMO','Age, Height and Weight Demographic data'),
	(2,'ORT','Opioid Replacement Therapy data'),
	(3,'STRESS', 'Stress Questionnaire');

TRUNCATE TABLE `tblSurveyField`;
INSERT INTO `tblSurveyField` (`survey_field_id`, `survey_type_id`, `field_name`, `field_display_name`, `is_deleted`)
VALUES
	(1,2,'alcoholism','Personal history of alcoholism',0),
	(2,2,'drugAbuse','Personal history of illegal drug abuse',0),
	(3,2,'presDrug','Personal history of prescription drug abuse',0),
	(8,2,'family-alcoholism','Family history of alcoholism',0),
	(9,2,'family-drugAbuse','Family history of illegal drug abuse',0),
	(10,2,'family-presDrug','Family history of prescription drug abuse ',0),
	(11,2,'age','Age between 16 and 45 old',0),
	(12,2,'mentalDisorders','Mental health disorders\n(e.g. Anxiety, Attention Deficit Disorder,\nObsessive Compulsive Disorder,\nBipolar Disorder, Schizophrenia)',0),
	(13,2,'depression','Depression',0),
	(14,3,'stress1','been upset because of something that happened unexpectedly?',0),
	(15,3,'stress2','felt you were unable to control the important things in your life?',0),
	(16,3,'stress3','felt nervous and "stressed"?',0),
	(17,3,'stress4','felt confident about your ability to handle your personal problems?',0),
	(18,3,'stress5','felt things were going your way?',0),
	(19,3,'stress6','found you could not cope with all the things that you had to do?',0),
	(20,3,'stress7','been able to control irritations in your life?',0),
	(21,3,'stress8','felt you were on top of things?',0),
	(22,3,'stress9','been angered because of things that were outside of your control?',0),
	(23,3,'stress10','felt difficulties were piling up so high that you could not overcome them?',0);

EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT

TRUNCATE TABLE `tblSurveyType`;
INSERT INTO `tblSurveyType` (`survey_type_id`, `survey_type_name`, `survey_type_description`)
VALUES
	(1,'DEMO','Age, Height and Weight Demographic data'),
	(2,'ORT','Opioid Replacement Therapy data');

TRUNCATE TABLE `tblSurveyField`;
INSERT INTO `tblSurveyField` (`survey_field_id`, `survey_type_id`, `field_name`, `field_display_name`, `is_deleted`)
VALUES
	(1,2,'alcoholism','Personal history of alcoholism',0),
	(2,2,'drugAbuse','Personal history of illegal drug abuse',0),
	(3,2,'presDrug','Personal history of prescription drug abuse',0),
	(8,2,'family-alcoholism','Family history of alcoholism',0),
	(9,2,'family-drugAbuse','Family history of illegal drug abuse',0),
	(10,2,'family-presDrug','Family history of prescription drug abuse ',0),
	(11,2,'age','Age between 16 and 45 old',0),
	(12,2,'mentalDisorders','Mental health disorders\n(e.g. Anxiety, Attention Deficit Disorder,\nObsessive Compulsive Disorder,\nBipolar Disorder, Schizophrenia)',0),
	(13,2,'depression','Depression',0);

EOT;
		$this->querySQL($sql);
	}

}
