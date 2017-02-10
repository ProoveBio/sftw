<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170210011411 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocument` ADD `test_id` INT NULL DEFAULT NULL 
    COMMENT 'Test_id this attachment belongs to if applicable' AFTER `external_sample_id`;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocument`
  DROP `test_id`;
EOT;
		$this->querySQL($sql);
	}

}
