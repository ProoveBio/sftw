<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150323190155 extends SchemaChange
{
	private $newSampleName = 'Batched Pending Intake';

	public function up()
	{
		$sql1 = <<< EOT
INSERT INTO tblSampleStatus
	(sample_status_name)
VALUES
	('$this->newSampleName');		
EOT;

		$sql2 = <<< EOT
ALTER TABLE tblSample
	MODIFY encounter_id INT(11) UNSIGNED DEFAULT NULL;
EOT;
		$sql3 = <<< EOT
ALTER TABLE tblSampleHistory
	MODIFY encounter_id INT(11) UNSIGNED DEFAULT NULL;
EOT;

		$this->querySQL($sql1);	
		$this->querySQL($sql2);	
		$this->querySQL($sql3);	
	}

	public function down()
	{
		$sql1 = <<< EOT
DELETE FROM tblSampleStatus WHERE sample_status_name = '$this->newSampleName';
EOT;
		$sql2 = <<< EOT
ALTER TABLE tblSampleStatus AUTO_INCREMENT = 1; 
EOT;

		$sql3 = <<< EOT
ALTER TABLE tblSample
	MODIFY encounter_id INT(11) UNSIGNED NOT NULL;
EOT;
		$sql4 = <<< EOT
ALTER TABLE tblSample
	MODIFY encounter_id INT(11) UNSIGNED NOT NULL;
EOT;

		$this->querySQL($sql1);
		$this->querySQL($sql2);
		$this->querySQL($sql3);
		$this->querySQL($sql4);
	}

}
