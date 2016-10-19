<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161019181858 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocument` ADD `original_created_datetime` TIMESTAMP NULL DEFAULT NULL 
    COMMENT 'The datetime this file was orignally created, rather than the record being created.' AFTER `is_deleted`;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocument`
  DROP `original_created_datetime`;
EOT;
		$this->querySQL($sql);
	}

}
