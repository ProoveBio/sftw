<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20151015213924 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
UPDATE `tblTestType` SET `parent_test_id` = 5 WHERE `test_type_id` = 6;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
UPDATE `tblTestType` SET `parent_test_id` = NULL WHERE `test_type_id` = 6;
EOT;
		$this->querySQL($sql);
	}

}
