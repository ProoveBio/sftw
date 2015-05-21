<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150521201730 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblTest` ADD `report_pdf_bucket` VARCHAR(127) NULL AFTER `is_deleted`, 
    ADD `report_pdf_key` VARCHAR(255) NULL AFTER `report_pdf_bucket`, 
    ADD `report_pdf_created_at` DATETIME NULL AFTER `report_pdf_key`;

ALTER TABLE `tblTestHistory` ADD `report_pdf_bucket` VARCHAR(127) NULL AFTER `is_deleted`, 
    ADD `report_pdf_key` VARCHAR(255) NULL AFTER `report_pdf_bucket`, 
    ADD `report_pdf_created_at` DATETIME NULL AFTER `report_pdf_key`;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblTest`
  DROP `report_pdf_bucket`,
  DROP `report_pdf_key`,
  DROP `report_pdf_created_at`;

ALTER TABLE `tblTestHistory`
  DROP `report_pdf_bucket`,
  DROP `report_pdf_key`,
  DROP `report_pdf_created_at`;
EOT;
		$this->querySQL($sql);
	}

}
