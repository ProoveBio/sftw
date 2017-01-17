<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170117180352 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
TRUNCATE TABLE `tblSampleStatus`;

INSERT INTO `tblSampleStatus` (`sample_status_id`, `sample_status_name`, `department`)
VALUES
	(1,'Received Pending Batch','Intake'),
	(2,'In Batch','Lab'),
	(3,'Lab Work Completed','Lab'),
	(4,'Requires Reswab','Customer Service'),
	(5,'Reswab Requested','N/A'),
	(6,'Batched Pending Intake',NULL),
	(7,'Pending Repeat',NULL);
EOT;


		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
TRUNCATE TABLE `tblSampleStatus`;

INSERT INTO `tblSampleStatus` (`sample_status_id`, `sample_status_name`, `department`)
VALUES
	(1,'Received Pending Batch','Intake'),
	(2,'In Batch','Lab'),
	(3,'Lab Work Completed','Lab'),
	(4,'Requires Reswab','Customer Service'),
	(5,'Reswab Requested','N/A'),
	(6,'Batched Pending Intake',NULL);
EOT;
		$this->querySQL($sql);
	}

}
