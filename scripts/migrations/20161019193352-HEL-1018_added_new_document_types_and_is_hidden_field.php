<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161019193352 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocumentType` ADD `is_hidden` TINYINT UNSIGNED NOT NULL DEFAULT '0' 
    COMMENT 'Is this field visible to Intake?' ;

INSERT INTO `tblDocumentType` 
(`document_type_id`, `type_name`, `type_description`, `is_hidden`) 
VALUES 
('101', 'PHOTO ID', 'Photo Identification', '0'),
('102', 'INS ID', 'Insurance Identification', '0'),
('103', 'PHYS Notes', 'Physician Chart Notes', '0'),
('104', 'PAT.SIG', 'Patient Signature', '1'),
('105', 'PAT.INIT', 'Patient Initial', '1'),
('106', 'PHY.SIG', 'Physician Signature', '1'),
('107', 'PHY.INIT', 'Physician Initial', '1'),
('108', 'PAT.HIS', 'Patient History', '1'),
('109', 'WIT.SIG', 'Witness Signature', '1'),
('110', 'WIT.INIT', 'Witness Initial', '1');
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblDocumentType`
  DROP `is_hidden`;

DELETE FROM `tblDocumentType` 
    WHERE `tblDocumentType`.`document_type_id` IN (101,102,103,104,105,106,107,108,109,110);
EOT;
		$this->querySQL($sql);
	}

}
