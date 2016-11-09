<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161103203507 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT

ALTER TABLE `tblSurveyField` ADD `sort_order` INT(11) DEFAULT NULL AFTER `field_display_name`;

TRUNCATE TABLE `tblSurveyType`;
INSERT INTO `tblSurveyType` (`survey_type_id`, `survey_type_name`, `survey_type_description`)
VALUES
	(1,'DEMO','Age, Height and Weight Demographic data'),
	(2,'ORT','Opioid Replacement Therapy data'),
	(3,'STRESS', 'Stress Questionnaire'),
	(4,'PAIN', 'Pain Points'),
	(5,'TMD', 'TMD Questionnaire');

TRUNCATE TABLE `tblSurveyField`;
INSERT INTO `tblSurveyField` (`survey_field_id`, `survey_type_id`, `field_name`, `field_display_name`, `sort_order`,`is_deleted`)
VALUES
	(1,2,'alcoholism','Personal history of alcoholism',1,0),
	(2,2,'drugAbuse','Personal history of illegal drug abuse',2,0),
	(3,2,'presDrug','Personal history of prescription drug abuse',3,0),
	(8,2,'family-alcoholism','Family history of alcoholism',4,0),
	(9,2,'family-drugAbuse','Family history of illegal drug abuse',5,0),
	(10,2,'family-presDrug','Family history of prescription drug abuse ',6,0),
	(11,2,'age','Age between 16 and 45 old',7,0),
	(12,2,'mentalDisorders','Mental health disorders\n(e.g. Anxiety, Attention Deficit Disorder,\nObsessive Compulsive Disorder,\nBipolar Disorder, Schizophrenia)',8,0),
	(13,2,'depression','Depression',9,0),
	(14,3,'stress1','been upset because of something that happened unexpectedly?',1,0),
	(15,3,'stress2','felt you were unable to control the important things in your life?',2,0),
	(16,3,'stress3','felt nervous and "stressed"?',3,0),
	(17,3,'stress4','felt confident about your ability to handle your personal problems?',7,0),
	(18,3,'stress5','felt things were going your way?',8,0),
	(19,3,'stress6','found you could not cope with all the things that you had to do?',4,0),
	(20,3,'stress7','been able to control irritations in your life?',9,0),
	(21,3,'stress8','felt you were on top of things?',10,0),
	(22,3,'stress9','been angered because of things that were outside of your control?',5,0),
	(23,3,'stress10','felt difficulties were piling up so high that you could not overcome them?',6,0),
	(24,4,'neck','Neck',1,0),
	(25,4,'upperBack','Upper back',2,0),
	(26,4,'chest','Chest',3,0),
	(27,4,'abdomen','Abdomen',4,0),
	(28,4,'lowerBack','Lower Back',5,0),
	(29,4,'jaw-left','Jaw (left)',6,0),
	(30,4,'jaw-right','Jaw (right)',7,0),
	(31,4,'shoulder-left','Shoulder (left)',8,0),
	(32,4,'shoulder-right','Shoulder (right)',9,0),
	(33,4,'upperArm-left','Upper arm (left)',10,0),
	(34,4,'upperArm-right','Upper arm (right)',11,0),
	(35,4,'lowerArm-left','Lower arm (left)',12,0),
	(36,4,'lowerArm-right','Lower arm (right)',13,0),
	(37,4,'hip-left','Hip/buttocks (left)',14,0),
	(38,4,'hip-right','Hip/buttocks (right)',15,0),
	(39,4,'upperLeg-left','Upper leg (left)',16,0),
	(40,4,'upperLeg-right','Upper leg (right)',17,0),
	(41,4,'lowerLeg-left','Lower leg (left)',18,0),
	(42,4,'lowerLeg-right','Lower leg (right)',19,0),
	(43,4,'fibroDiagnosis','Previously Diagnosed with Fibromyalgia',20,0),
	(44,4,'otherPain','Others:',21,0),
	(45,5,'painDuration','on average, how long did any pain in your jaw or temple area on either side last?',1,0),
	(46,5,'stiffness','have you had pain or stiffness in your jaw on awakening?',2,0),
	(47,5,'chewing','Chewing hard or tough food',3,0),
	(48,5,'opening','Opening your mouth or moving your jaw foward or to the side',4,0),
	(49,5,'grinding','Jaw habits such as holding teeth together, clenching, grinding, or chewing gum',5,0),
	(50,5,'talking','Other jaw activities such as talking, kissing, or yawning',6,0),
	(51,5,'tmdDiagnosis','Previously diagnosed with TMD',7,0);

DELETE FROM `tblTestTypeCustom` WHERE `test_type_id` = 27;

EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT

ALTER TABLE `tblSurveyField` DROP COLUMN `sort_order`;

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

INSERT INTO `tblTestTypeCustom` VALUES
	(25,27,'Neck','neck','checkbox',1),
	(26,27,'Upper back','upperBack','checkbox',2),
	(27,27,'Chest','chest','checkbox',3),
	(28,27,'Abdomen','abdomen','checkbox',4),
	(29,27,'Lower Back','lowerBack','checkbox',5),
	(30,27,'Jaw (left)','jaw-left','checkbox',6),
	(31,27,'Jaw (right)','jaw-right','checkbox',7),
	(32,27,'Shoulder (left)','shoulder-left','checkbox',8),
	(33,27,'Shoulder (right)','shoulder-right','checkbox',9),
	(34,27,'Upper arm (left)','upperArm-left','checkbox',10),
	(35,27,'Upper arm (right)','upperArm-right','checkbox',11),
	(36,27,'Lower arm (left)','lowerArm-left','checkbox',12),
	(37,27,'Lower arm (right)','lowerArm-right','checkbox',13),
	(38,27,'Hip/buttocks (left)','hip-left','checkbox',14),
	(39,27,'Hip/buttocks (right)','hip-right','checkbox',15),
	(40,27,'Upper leg (left)','upperLeg-left','checkbox',16),
	(41,27,'Upper leg (right)','upperLeg-right','checkbox',17),
	(42,27,'Lower leg (left)','lowerLeg-left','checkbox',18),
	(43,27,'Lower leg (right)','lowerLeg-right','checkbox',19);

EOT;
		$this->querySQL($sql);
	}

}
