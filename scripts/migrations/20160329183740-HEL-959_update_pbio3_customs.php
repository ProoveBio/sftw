<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20160329183740 extends SchemaChange
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
(24,6,'Cytochrome P-450 3A5','p450-3a5','checkbox',11);
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
(16,6,'Cytochrome P-450 2E1','p450-2e1','checkbox',4),
(17,6,'Cytochrome P-450 3A4','p450-3a4','checkbox',5),
(18,6,'UDP-Glucuronosyltransferase-2B7 (UGT2B7)','ugt2b7','checkbox',6),
(19,6,'Vitamin K epoxide reductase complex subunit 1 (VKORC1)','2korc1','checkbox',7),
(20,6,'Cytochrome P-450 1A2','p450-1a2','checkbox',8),
(21,6,'Cytochrome P-450 2C9','p450-2c9','checkbox',9),
(22,6,'Cytochrome P-450 2D6','p450-2d6','checkbox',10),
(23,6,'Cytochrome P-450 2J2','p450-2j2','checkbox',11),
(24,6,'Cytochrome P-450 3A5','p450-3a5','checkbox',12);
EOT;
		$this->querySQL($sql);
	}

}
