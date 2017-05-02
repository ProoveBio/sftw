<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170502225817 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
CREATE TABLE IF NOT EXISTS `tblCorrectionCode` (
`correctioncode_id` int(10) unsigned NOT NULL,
  `code` varchar(16) NOT NULL COMMENT 'Test report correction code',
  `description` varchar(255) NOT NULL COMMENT 'Correction code description',
  `dos_update` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Update DoS on the report?',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Is active?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tblTestAmendment` (
`amendment_id` int(10) unsigned NOT NULL,
  `test_id` int(10) unsigned NOT NULL COMMENT 'Test id (fk)',
  `code_id` int(10) unsigned NOT NULL COMMENT 'Test report correction code id (tblCorrectionCode)',
  `annotation` varchar(255) DEFAULT NULL COMMENT 'Amendment message',
  `created_datetime` datetime NOT NULL,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tblCorrectionCode`
 ADD PRIMARY KEY (`correctioncode_id`);

ALTER TABLE `tblTestAmendment`
 ADD PRIMARY KEY (`amendment_id`), ADD KEY `test_id` (`test_id`), ADD KEY `code_id` (`code_id`);

ALTER TABLE `tblCorrectionCode`
MODIFY `correctioncode_id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `tblTestAmendment`
MODIFY `amendment_id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `tblTestAmendment`
ADD CONSTRAINT `fk_correctioncode_id` FOREIGN KEY (`code_id`) REFERENCES `tblCorrectionCode` (`correctioncode_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
ADD CONSTRAINT `fk_test_id` FOREIGN KEY (`test_id`) REFERENCES `tblTest` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP TABLE IF EXISTS `tblTestAmendment`, `tblCorrectionCode`;
EOT;
		$this->querySQL($sql);
	}

}
