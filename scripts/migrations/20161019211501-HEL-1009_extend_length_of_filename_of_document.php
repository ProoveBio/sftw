<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161019211501 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocument` CHANGE `original_filename` `original_filename` VARCHAR(255) 
    CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
-- Write your migration SQL here
EOT;
		$this->querySQL($sql);
	}

}
