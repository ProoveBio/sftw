<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150314184138 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
-- Write your migration SQL here
-- ------------------------------------------
-- Schema Changes
-- ------------------------------------------
--
-- Table structure for table `tblDocument`
--

DROP TABLE IF EXISTS `tblDocument`;

CREATE TABLE `tblDocument` (
  `document_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `encounter_id` int(11) unsigned DEFAULT NULL,
  `filename` varchar(50) NOT NULL,
  `path` varchar(255) NOT NULL,
  `bucket` varchar(45) NOT NULL,
  `original_filename` varchar(50) NOT NULL,
  `document_type_id` smallint(5) unsigned NULL,
  `external_sample_id` varchar(45) NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_datetime` datetime NOT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `modified_by` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tblDocumentType`
--

DROP TABLE IF EXISTS `tblDocumentType`;

CREATE TABLE `tblDocumentType` (
  `document_type_id` smallint(6) unsigned NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_description` text,
  PRIMARY KEY (`document_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP TABLE IF EXISTS `tblDocument`;
DROP TABLE IF EXISTS `tblDocumentType`;
EOT;
		$this->querySQL($sql);
	}

}
