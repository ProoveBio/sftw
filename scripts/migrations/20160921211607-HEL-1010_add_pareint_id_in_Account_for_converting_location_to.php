<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20160921211607 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblAccount` 
    ADD `parent_id` INT(10) UNSIGNED DEFAULT NULL AFTER `account_id`;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblAccount`
  DROP `parent_id`;
EOT;
		$this->querySQL($sql);
	}

}
