<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20160930000402 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblAccount` 
    CHANGE `account_num` `account_num` VARCHAR(63) NULL DEFAULT NULL COMMENT 'Account Number from Salesforce';
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
