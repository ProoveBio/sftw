<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161005203257 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT

TRUNCATE TABLE `tblTestStatus`;

INSERT INTO `tblTestStatus` 
VALUES (1,'Pending Sample Analysis','Lab'),
(2,'Pending Sata Correction','Customer Service'),
(3,'Pending Report QC','Reporting'),
(4,'Pending Billing','Billing'),
(5,'Downloaded Billing','Billing'),
(6,'Billed','Billing'),
(7,'Test Failed','N/A'),
(8,'Error','N/A'),
(9,'Lab Rejected','N/A');

EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT

TRUNCATE TABLE `tblTestStatus`;

INSERT INTO `tblTestStatus` 
VALUES (1,'Pending Sample Analysis','Lab'),
(2,'Pending Sata Correction','Customer Service'),
(3,'Pending Report QC','Reporting'),
(4,'Pending Billing','Billing'),
(5,'Downloaded Billing','Billing'),
(6,'Billed','Billing'),
(7,'Test Failed','N/A'),
(8,'Error','N/A');

EOT;
		$this->querySQL($sql);
	}

}
