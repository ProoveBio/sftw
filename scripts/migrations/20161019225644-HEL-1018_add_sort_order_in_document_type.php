<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161019225644 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocumentType` ADD `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT '0' ;

UPDATE `tblDocumentType` SET `sort_order` = '1'  WHERE `tblDocumentType`.`document_type_id` = 1;
UPDATE `tblDocumentType` SET `sort_order` = '0'  WHERE `tblDocumentType`.`document_type_id` = 1;
UPDATE `tblDocumentType` SET `sort_order` = '5'  WHERE `tblDocumentType`.`document_type_id` = 2;
UPDATE `tblDocumentType` SET `sort_order` = '10' WHERE `tblDocumentType`.`document_type_id` = 3;
UPDATE `tblDocumentType` SET `sort_order` = '15' WHERE `tblDocumentType`.`document_type_id` = 4;
UPDATE `tblDocumentType` SET `sort_order` = '20' WHERE `tblDocumentType`.`document_type_id` = 5;
UPDATE `tblDocumentType` SET `sort_order` = '25' WHERE `tblDocumentType`.`document_type_id` = 6;
UPDATE `tblDocumentType` SET `sort_order` = '30' WHERE `tblDocumentType`.`document_type_id` = 7;
UPDATE `tblDocumentType` SET `sort_order` = '35' WHERE `tblDocumentType`.`document_type_id` = 8;
UPDATE `tblDocumentType` SET `sort_order` = '40' WHERE `tblDocumentType`.`document_type_id` = 9;
UPDATE `tblDocumentType` SET `sort_order` = '85' WHERE `tblDocumentType`.`document_type_id` = 10;
UPDATE `tblDocumentType` SET `sort_order` = '45' WHERE `tblDocumentType`.`document_type_id` = 101;
UPDATE `tblDocumentType` SET `sort_order` = '50' WHERE `tblDocumentType`.`document_type_id` = 102;
UPDATE `tblDocumentType` SET `sort_order` = '55' WHERE `tblDocumentType`.`document_type_id` = 103;
UPDATE `tblDocumentType` SET `sort_order` = '60' WHERE `tblDocumentType`.`document_type_id` = 104;
UPDATE `tblDocumentType` SET `sort_order` = '65' WHERE `tblDocumentType`.`document_type_id` = 105;
UPDATE `tblDocumentType` SET `sort_order` = '70' WHERE `tblDocumentType`.`document_type_id` = 106;
UPDATE `tblDocumentType` SET `sort_order` = '75' WHERE `tblDocumentType`.`document_type_id` = 107;
UPDATE `tblDocumentType` SET `sort_order` = '80' WHERE `tblDocumentType`.`document_type_id` = 108;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocumentType`
  DROP `sort_order`;
EOT;
		$this->querySQL($sql);
	}

}
