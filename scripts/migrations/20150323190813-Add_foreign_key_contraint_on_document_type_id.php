<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150323190813 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocument`
	ADD CONSTRAINT `tblDocument_fk_document_type_id` FOREIGN KEY (`document_type_id`) 
	REFERENCES `tblDocumentType`(`document_type_id`)
	ON DELETE SET NULL ON UPDATE CASCADE;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocument` DROP FOREIGN KEY `tblDocument_fk_document_type_id`;
EOT;
		$this->querySQL($sql);
	}

}
