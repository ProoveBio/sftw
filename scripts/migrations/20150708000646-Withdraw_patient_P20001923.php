<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150708000646 extends SchemaChange
{

	public function up()
	{
        $patient_id = "2X13514413";

        $sql = <<< EOT
SELECT E.encounter_id, S.sample_id, P.patient_id, group_concat(C.contact_id) AS contact_ids
FROM tblEncounter E
INNER JOIN tblSample S ON S.encounter_id = E.encounter_id
INNER JOIN tblPatient P ON E.patient_id = P.patient_id
INNER JOIN tblContact C ON P.contact_id = C.contact_id
WHERE P.external_patient_id = '{$patient_id}'
EOT;
	    $sth = $this->pdo->query($sql);
        $indexes = $sth->fetch();

        $contact_ids = explode(',', $indexes['contact_ids']);
        $contact_ids =implode('","', $contact_ids);

	    $this->pdo->beginTransaction();

	    try {
	        $sql = <<< EOT
DELETE FROM `tblEncounter` WHERE `encounter_id` = "{$indexes['encounter_id']}";

DELETE FROM `tblSample` WHERE `sample_id` = "{$indexes['sample_id']}";

DELETE FROM `tblTest` WHERE `encounter_id` = "{$indexes['encounter_id']}";

DELETE FROM `tblPatient` WHERE `patient_id` = "{$indexes['patient_id']}";

DELETE FROM `tblContact` WHERE `contact_id` IN ("{$contact_ids}");

DELETE FROM `tblSampleBatch` WHERE `sample_id` = "{$indexes['sample_id']}";

DELETE FROM `tblEncounterPayer` WHERE `encounter_id` = "{$indexes['encounter_id']}";
EOT;
	        $this->querySQL($sql);

	        $this->pdo->commit();
	    } catch (Exception $e) {
	        $this->pdo->rollBack();
	        throw $e;
	    }
	}

	public function down()
	{
		$sql = <<< EOT
-- Write your migration SQL here
EOT;
		$this->querySQL($sql);
	}

}
