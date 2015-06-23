<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150623191721 extends SchemaChange
{

	public function up()
	{
		$sql1 = <<< EOT
ALTER TABLE `tblTestType` ADD COLUMN `is_summary` TINYINT(1) NOT NULL DEFAULT 0;
EOT;

		$sql2 = <<< EOT
INSERT INTO `tblTestType` (`display_test_name`,`external_test_type_id`,`test_type_name`,`test_version`,`is_active`,`is_summary`)
VALUES ('SUMMARY2','SUMMARY2','Proove Summary View', 2, 1, 1);
EOT;
		$this->querySQL($sql1);	
		$this->querySQL($sql2);	
	}

	public function down()
	{
		$sql1 = <<< EOT
ALTER TABLE `tblTestType` DROP COLUMN `is_summary`;
EOT;

		$sql2 = <<< EOT
DELETE FROM `tblTestType` WHERE `external_test_type_id` = 'SUMMARY2';
EOT;

		$sql3 = <<< EOT
ALTER TABLE `tblTestType` AUTO_INCREMENT = 1;
EOT;
		$this->querySQL($sql1);
		$this->querySQL($sql2);
		$this->querySQL($sql3);
	}

}
