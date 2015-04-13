<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150413175136 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CytoP450-2B6', `custom_name`='p450-2b6' WHERE `custom_name` = 'p450-1a1'
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
UPDATE `tblTestTypeCustom` SET `custom_display_name` = 'CytoP450-1A1', `custom_name`='p450-1a1' WHERE `custom_name` = 'p450-2b6'
EOT;
		$this->querySQL($sql);
	}

}
