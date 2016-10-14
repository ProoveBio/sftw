<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161014014729 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblAccount` ADD `sf_location_id` VARCHAR(32) NULL DEFAULT NULL 
COMMENT 'reference of location id where this clinic account convert from. It''s for migration ONLY, DO NOT use it in the future' AFTER `is_deleted`;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblAccount`
  DROP `sf_location_id`;
EOT;
		$this->querySQL($sql);
	}

}
