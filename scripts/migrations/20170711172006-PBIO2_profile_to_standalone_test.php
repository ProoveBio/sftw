<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170711172006 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
INSERT INTO `tblTestType` 
  (`test_type_id`, `display_test_name`, `external_test_type_id`, `test_type_name`, `parent_test_id`, `test_version`, `is_active`, `is_summary`) 
  VALUES (43, 'PBI_02D', 'PBIO2D', 'Proove Drug Metabolism Neurologics', '5', '1', '1', '0');
UPDATE tblTestType SET is_active = 1 WHERE display_test_name IN ('PBI_02A','PBI_02B','PBI_02C');
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
UPDATE tblTestType SET is_active = 0 WHERE display_test_name IN ('PBI_02A','PBI_02B','PBI_02C','PBI_02D');
DELETE FROM tblTestType WHERE test_type_id = 43;
EOT;
		$this->querySQL($sql);
	}

}
