<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170124225535 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT

ALTER TABLE `tblDocument` ADD `test_id` INT(11) UNSIGNED AFTER `encounter_id`;

EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT

ALTER TABLE `tblDocument` DROP COLUMN `test_id`;

EOT;
		$this->querySQL($sql);
	}

}
