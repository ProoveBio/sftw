<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20160630220253 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT

TRUNCATE TABLE `tblTestTypeCustom`;

INSERT INTO `tblTestTypeCustom` 
VALUES (1,2,'Personal history of alcoholism','alcoholism','checkbox',1),
(2,2,'Personal history of illegal drug abuse','drugAbuse','checkbox',2),
(3,2,'Personal history of prescription drug abuse','presDrug','checkbox',3),
(4,2,'Age between 16 and 45 old','age','checkbox',4),
(5,2,'Mental health disorders\n(e.g. Anxiety, Attention Deficit Disorder,\nObsessive Compulsive Disorder,\nBipolar Disorder, Schizophrenia)','mentalDisorders','checkbox',5),
(6,2,'Depression','depression','checkbox',6),
(13,6,'Cytochrome P-450 2B6','p450-2b6','checkbox',1),
(14,6,'Cytochrome P-450 2C8','p450-2c8','checkbox',2),
(15,6,'Cytochrome P-450 2C19','p450-2c19','checkbox',3),
(17,6,'Cytochrome P-450 3A4','p450-3a4','checkbox',4),
(18,6,'UDP-Glucuronosyltransferase-2B7 (UGT2B7)','ugt2b7','checkbox',5),
(10,6,'UDP-Glucuronosyltransferase-2B15 (UGT2B15)','ugt2b15','checkbox',6),
(19,6,'Vitamin K epoxide reductase complex subunit 1 (VKORC1)','2korc1','checkbox',7),
(20,6,'Cytochrome P-450 1A2','p450-1a2','checkbox',8),
(21,6,'Cytochrome P-450 2C9','p450-2c9','checkbox',9),
(22,6,'Cytochrome P-450 2D6','p450-2d6','checkbox',10),
(24,6,'Cytochrome P-450 3A5','p450-3a5','checkbox',11),
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
(43,27,'Lower leg (right)','lowerLeg-right','checkbox',19),
(44,25,'Patient Initial','initial','checkbox',1);


EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT

TRUNCATE TABLE `tblTestTypeCustom`;

INSERT INTO `tblTestTypeCustom` 
VALUES (1,2,'Personal history of alcoholism','alcoholism','checkbox',1),
(2,2,'Personal history of illegal drug abuse','drugAbuse','checkbox',2),
(3,2,'Personal history of prescription drug abuse','presDrug','checkbox',3),
(4,2,'Age between 16 and 45 old','age','checkbox',4),
(5,2,'Mental health disorders\n(e.g. Anxiety, Attention Deficit Disorder,\nObsessive Compulsive Disorder,\nBipolar Disorder, Schizophrenia)','mentalDisorders','checkbox',5),
(6,2,'Depression','depression','checkbox',6),
(13,6,'Cytochrome P-450 2B6','p450-2b6','checkbox',1),
(14,6,'Cytochrome P-450 2C8','p450-2c8','checkbox',2),
(15,6,'Cytochrome P-450 2C19','p450-2c19','checkbox',3),
(17,6,'Cytochrome P-450 3A4','p450-3a4','checkbox',4),
(18,6,'UDP-Glucuronosyltransferase-2B7 (UGT2B7)','ugt2b7','checkbox',5),
(10,6,'UDP-Glucuronosyltransferase-2B15 (UGT2B15)','ugt2b15','checkbox',6),
(19,6,'Vitamin K epoxide reductase complex subunit 1 (VKORC1)','2korc1','checkbox',7),
(20,6,'Cytochrome P-450 1A2','p450-1a2','checkbox',8),
(21,6,'Cytochrome P-450 2C9','p450-2c9','checkbox',9),
(22,6,'Cytochrome P-450 2D6','p450-2d6','checkbox',10),
(24,6,'Cytochrome P-450 3A5','p450-3a5','checkbox',11),
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
