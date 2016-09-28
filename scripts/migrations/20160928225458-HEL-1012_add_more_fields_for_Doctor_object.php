<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20160928225458 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblDoctor` 
    ADD `status` VARCHAR(64) NULL DEFAULT NULL COMMENT 'Salesforce contact status' AFTER `tricare`, 
    ADD `protocols` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Protocols the doctor enrolled in' AFTER `status`, 
    ADD `specialties` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Medical specialties' AFTER `protocols`;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblDoctor`
  DROP `status`,
  DROP `protocols`,
  DROP `specialties`;
EOT;
		$this->querySQL($sql);
	}

}
