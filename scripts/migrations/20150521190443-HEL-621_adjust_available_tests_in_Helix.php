<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150521190443 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
TRUNCATE TABLE `tblTestType`;

INSERT INTO `tblTestType` VALUES (1,'N/A','N/A','UNKNOWN',NULL,1,1),
(2,'PBI_01','PBIO1','Proove Opioid Risk',NULL,1,1),
(3,'PBI_01','PBIO1','Proove Opioid Risk',NULL,2,1),
(4,'PBI_02','PBIO2','Proove Drug Metabolism Comprehensive',NULL,1,1),
(5,'PBI_02','PBIO2','Proove Drug Metabolism Comprehensive',NULL,2,1),
(6,'PBI_03','PBIO3','Proove Drug Metabolism Custom',NULL,1,1),
(7,'PBI_04','PBIO4','Proove Pain Perception',NULL,1,1),
(8,'PBI_05','PBIO5','Proove Opioid Response Comprehensive',NULL,1,1),
(9,'PBI_06','PBIO6','Proove Hydrocodone',8,1,1),
(10,'PBI_07','PBIO7','Proove Oxycodone',8,1,1),
(11,'PBI_08','PBIO8','Proove Morphine',8,1,1),
(12,'PBI_09','PBIO9','Proove Tramadol',8,1,1),
(13,'PBI_010','PBIO10','Proove Hydromorphone',8,1,1),
(14,'PBI_011','PBIO11','Proove Non Opioid Response Comprehensive',NULL,1,1),
(15,'PBI_012','PBIO12','Proove Ibuprofen',14,1,1),
(16,'PBI_013','PBIO13','Proove Gabapentin',14,1,1),
(17,'PBI_014','PBIO14','Proove Alpazolam',14,1,1),
(18,'PBI_015','PBIO15','Proove Duloxetine',14,1,1),
(19,'PBI_016','PBIO16','Proove Acetaminophen',14,1,1),
(20,'PBI_017','PBIO17','Proove NSAID Risk',NULL,1,1),
(21,'PBI_018','PBIO18','Proove MAT',NULL,1,1),
(22,'PBI_019','PBIO19','Proove Methadone',21,1,1),
(23,'PBI_020','PBIO20','Proove Buprenorphine/Nalaxone',21,1,1),
(24,'PBI_021','PBIO21','Proove Epidural w/Fentanyl',NULL,1,1),
(25,'PBI_022','PBIO22','Proove Psychiatric Disorders Profile',NULL,1,0),
(26,'PBI_023','PBIO23','Proove Depression',25,1,0),
(27,'PBI_024','PBIO24','Proove Anxiety & Stress Risk',25,1,0),
(28,'PBI_025','PBIO25','Proove Fibromyalga Syndrome Risk',25,1,0),
(29,'PBI_026','PBIO26','Proove Migraine Risk',25,1,0),
(30,'PBI_027','PBIO27','Proove ADHD',25,1,0),
(31,'PBI_028','PBIO28','Proove Bipolar Disorder Profile',25,1,0),
(32,'PBI_029','PBIO29','Proove Neonatal Abstinence Syndrome',NULL,1,0),
(33,'PBI_030','PBIO30','Proove Opioid-Induced Respiratory Depression',NULL,1,0),
(34,'PBI_031','PBIO31','Proove Psychiatry Response Comprehensive Profile',NULL,1,0),
(35,'PBI_032','PBIO32','Proove Cardiovascular Disorder Profile Profile',NULL,1,0),
(36,'PBI_033','PBIO33','Proove Lipid Metabolism Disorder Risk Profile',35,1,0),
(37,'PBI_034','PBIO34','Proove Stress-Related Hypertension Profile',35,1,0),
(38,'PBI_035','PBIO35','Proove Cardiovascular Response Comprehensive Profile',NULL,1,0);
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
TRUNCATE TABLE `tblTestType`;

INSERT INTO `tblTestType` VALUES (1,'N/A','N/A','UNKNOWN',NULL,1,1),
(2,'PBI_01','PBIO1','Proove Opioid Risk',NULL,1,1),
(3,'PBI_01','PBIO1','Proove Opioid Risk',NULL,2,1),
(4,'PBI_02','PBIO2','Proove Drug Metabolism Comprehensive',NULL,1,1),
(5,'PBI_02','PBIO2','Proove Drug Metabolism Comprehensive',NULL,2,1),
(6,'PBI_03','PBIO3','Proove Drug Metabolism Custom',NULL,1,1),
(7,'PBI_04','PBIO4','Proove Pain Perception',NULL,1,1),
(8,'PBI_05','PBIO5','Proove Opioid Response Comprehensive',NULL,1,1),
(9,'PBI_06','PBIO6','Proove Hydrocodone',8,1,1),
(10,'PBI_07','PBIO7','Proove Oxycodone',8,1,1),
(11,'PBI_08','PBIO8','Proove Morphine',8,1,1),
(12,'PBI_09','PBIO9','Proove Tramadol',8,1,1),
(13,'PBI_010','PBIO10','Proove Hydromorphone',8,1,1),
(14,'PBI_011','PBIO11','Proove Non Opioid Response Comprehensive',NULL,1,1),
(15,'PBI_012','PBIO12','Proove Ibuprofen',14,1,1),
(16,'PBI_013','PBIO13','Proove Gabapentin',14,1,1),
(17,'PBI_014','PBIO14','Proove Alpazolam',14,1,1),
(18,'PBI_015','PBIO15','Proove Duloxetine',14,1,1),
(19,'PBI_016','PBIO16','Proove Acetaminophen',14,1,1),
(20,'PBI_017','PBIO17','Proove NSAID Risk',NULL,1,1),
(21,'PBI_018','PBIO18','Proove MAT',NULL,1,1),
(22,'PBI_019','PBIO19','Proove Methadone',21,1,1),
(23,'PBI_020','PBIO20','Proove Buprenorphine/Nalaxone',21,1,1),
(24,'PBI_021','PBIO21','Proove Epidural w/Fentanyl',NULL,1,1),
(25,'PBI_022','PBIO22','Proove Co-Occurring Disorders',NULL,1,1),
(26,'PBI_023','PBIO23','Proove Depression',25,1,1),
(27,'PBI_024','PBIO24','Proove Anxiety & Stress Risk',25,1,1),
(28,'PBI_025','PBIO25','Proove Fibromyalga Syndrome Risk',25,1,1),
(29,'PBI_026','PBIO26','Proove Migraine Risk',25,1,1),
(30,'PBI_027','PBIO27','Proove ADHD',25,1,1),
(31,'PBI_028','PBIO28','Proove TemporomandibularJoint Dysfunction Risk',25,1,1),
(32,'PBI_029','PBIO29','Proove Neonatal Abstinence Syndrome',25,1,1),
(33,'PBI_030','PBIO30','Proove Opioid-Induced Respiratory Depression',25,1,1);
EOT;
		$this->querySQL($sql);
	}

}
