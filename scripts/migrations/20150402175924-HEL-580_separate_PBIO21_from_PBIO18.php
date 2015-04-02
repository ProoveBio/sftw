<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150402175924 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
UPDATE tblTestType SET parent_test_id = NULL WHERE external_test_type_id = "PBIO21";
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
UPDATE tblTestType SET parent_test_id = 21 WHERE external_test_type_id = "PBIO21";
EOT;
		$this->querySQL($sql);
	}

}
