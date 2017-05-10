<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170509235548 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblPayerType` ADD `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ; 
UPDATE `tblPayerType` SET `is_active` = 0 WHERE `payer_type_id` IN (1,3,8) ;
UPDATE `tblPayerType` SET `payer_type_name` = 'Pro Bono' WHERE `payer_type_name` = 'Patient' ;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
UPDATE `tblPayerType` SET `payer_type_name` = 'Patient' WHERE `payer_type_name` = 'Pro Bono' ;
ALTER TABLE `tblPayerType` DROP `is_active`;
EOT;
		$this->querySQL($sql);
	}

}
