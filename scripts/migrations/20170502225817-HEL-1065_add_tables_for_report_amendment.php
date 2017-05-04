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
--
-- Table structure for table `tblCorrectionCode`
--

CREATE TABLE IF NOT EXISTS `tblCorrectionCode` (
`correctioncode_id` int(10) unsigned NOT NULL,
  `code` varchar(16) NOT NULL COMMENT 'Test report correction code',
  `description` varchar(255) NOT NULL COMMENT 'Correction code description',
  `dos_update` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Update DoS on the report?',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Is active?'
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblTestAmendment`
--

CREATE TABLE IF NOT EXISTS `tblTestAmendment` (
`amendment_id` int(10) unsigned NOT NULL,
  `test_id` int(10) unsigned NOT NULL COMMENT 'Test id (fk)',
  `annotation` varchar(255) DEFAULT NULL COMMENT 'Amendment message',
  `created_datetime` datetime NOT NULL,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tblTestCorrection`
--

CREATE TABLE IF NOT EXISTS `tblTestCorrection` (
`test_correction_id` int(10) unsigned NOT NULL,
  `amendment_id` int(10) unsigned NOT NULL COMMENT 'Test amendment id (fk)',
  `correctioncode_id` int(10) unsigned NOT NULL COMMENT 'Test report correction code id (fk)',
  `created_datetime` datetime NOT NULL,
  `modified_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='All correction code associated with an amendment';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblCorrectionCode`
--
ALTER TABLE `tblCorrectionCode`
 ADD PRIMARY KEY (`correctioncode_id`);

--
-- Indexes for table `tblTestAmendment`
--
ALTER TABLE `tblTestAmendment`
 ADD PRIMARY KEY (`amendment_id`), ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `tblTestCorrection`
--
ALTER TABLE `tblTestCorrection`
 ADD PRIMARY KEY (`test_correction_id`), ADD KEY `amendment_id` (`amendment_id`), ADD KEY `correctioncode_id` (`correctioncode_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblCorrectionCode`
--
ALTER TABLE `tblCorrectionCode`
MODIFY `correctioncode_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tblTestAmendment`
--
ALTER TABLE `tblTestAmendment`
MODIFY `amendment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tblTestCorrection`
--
ALTER TABLE `tblTestCorrection`
MODIFY `test_correction_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblTestAmendment`
--
ALTER TABLE `tblTestAmendment`
ADD CONSTRAINT `fk_test_id` FOREIGN KEY (`test_id`) REFERENCES `tblTest` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tblTestCorrection`
--
ALTER TABLE `tblTestCorrection`
ADD CONSTRAINT `fk_correction_code_id` FOREIGN KEY (`correctioncode_id`) REFERENCES `tblCorrectionCode` (`correctioncode_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_test_amendment_id` FOREIGN KEY (`amendment_id`) REFERENCES `tblTestAmendment` (`amendment_id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP TABLE IF EXISTS `tblTestCorrection`, `tblTestAmendment`, `tblCorrectionCode`;
EOT;
		$this->querySQL($sql);
	}

}
