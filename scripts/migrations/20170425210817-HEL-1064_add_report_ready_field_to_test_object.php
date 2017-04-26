<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170425210817 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblTest` ADD `report_upload_ready` TINYINT UNSIGNED NOT NULL DEFAULT '0' 
    COMMENT 'Is test report ready for physican portal?' AFTER `report_pdf_created_at`;

ALTER TABLE `tblTestHistory` ADD `report_upload_ready` TINYINT UNSIGNED NOT NULL DEFAULT '0' 
    COMMENT 'Is test report ready for physican portal?' AFTER `report_pdf_created_at`;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblTest`
    DROP `report_upload_ready`;

ALTER TABLE `tblTestHistory`
    DROP `report_upload_ready`;
EOT;
		$this->querySQL($sql);
	}

}
