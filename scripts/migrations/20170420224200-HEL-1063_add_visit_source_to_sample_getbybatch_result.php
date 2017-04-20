<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20170420224200 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Sample_GetByBatch`;
CREATE PROCEDURE `pr_Sample_GetByBatch`(
	IN new_session VARCHAR(255),
	IN s_batch_id  INT UNSIGNED
)
this:BEGIN
	DECLARE is_valid_session TINYINT UNSIGNED DEFAULT 0;
	DECLARE calling_user_id INT UNSIGNED DEFAULT 1;
	IF (new_session != '04b71978a1bf4368a176f519faded7eff7c05febf8e74f6cb732f1e43adfbc1a') THEN
	BEGIN
		CALL pr_UserSession_Validate(new_session, is_valid_session, calling_user_id);
		IF (is_valid_session != 1) THEN
		BEGIN
			SIGNAL SQLSTATE '45001'
				SET MESSAGE_TEXT = 'Invalid session for procedure.';
			LEAVE this;
		END;
		END IF;
	END;
	END IF;
	
	SELECT
		 T.*
		,SB.sequence_num
		,SB.is_failed
		,P_C.first_name AS patient_first_name
		,P_C.last_name AS patient_last_name
		,D_C.first_name AS doctor_first_name
		,D_C.last_name AS doctor_last_name
		,U.first_name AS created_by_first_name
		,U.last_name AS created_by_last_name
		,EU.first_name AS intaked_by_first_name
		,EU.last_name AS intaked_by_last_name
		,E.created_datetime AS intaked_datetime
		,P.birth_date
		,SS.sample_status_name
        ,E.is_qced
        ,E.qced_at
        ,E.original_source AS `visit_source`
        ,QC_U.first_name AS qc_by_first_name
        ,QC_U.last_name AS qc_by_last_name
	FROM
		tblSample T
		INNER JOIN tblSampleBatch SB ON T.sample_id = SB.sample_id
		LEFT OUTER JOIN tblUser U ON T.created_by = U.user_id
        LEFT OUTER JOIN tblEncounter E ON T.encounter_id = E.encounter_id
        LEFT OUTER JOIN tblUser QC_U ON E.qced_by = QC_U.user_id
        LEFT OUTER JOIN tblUser EU ON E.created_by = EU.user_id
		LEFT OUTER JOIN tblPatient P ON E.patient_id = P.patient_id
		LEFT OUTER JOIN tblContact P_C ON P.contact_id = P_C.contact_id
		LEFT OUTER JOIN tblDoctor D ON E.doctor_id = D.doctor_id
		LEFT OUTER JOIN tblContact D_C ON D.contact_id = D_C.contact_id
		LEFT OUTER JOIN tblSampleStatus SS ON T.sample_status_id = SS.sample_status_id
	WHERE
		SB.batch_id = s_batch_id
		AND
		SB.is_deleted = 0
	ORDER BY SB.sequence_num ASC;
END
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Sample_GetByBatch`;
CREATE PROCEDURE `pr_Sample_GetByBatch`(
	IN new_session VARCHAR(255),
	IN s_batch_id  INT UNSIGNED
)
this:BEGIN
	DECLARE is_valid_session TINYINT UNSIGNED DEFAULT 0;
	DECLARE calling_user_id INT UNSIGNED DEFAULT 1;
	IF (new_session != '04b71978a1bf4368a176f519faded7eff7c05febf8e74f6cb732f1e43adfbc1a') THEN
	BEGIN
		CALL pr_UserSession_Validate(new_session, is_valid_session, calling_user_id);
		IF (is_valid_session != 1) THEN
		BEGIN
			SIGNAL SQLSTATE '45001'
				SET MESSAGE_TEXT = 'Invalid session for procedure.';
			LEAVE this;
		END;
		END IF;
	END;
	END IF;
	
	SELECT
		 T.*
		,SB.sequence_num
		,SB.is_failed
		,P_C.first_name AS patient_first_name
		,P_C.last_name AS patient_last_name
		,D_C.first_name AS doctor_first_name
		,D_C.last_name AS doctor_last_name
		,U.first_name AS created_by_first_name
		,U.last_name AS created_by_last_name
		,EU.first_name AS intaked_by_first_name
		,EU.last_name AS intaked_by_last_name
		,E.created_datetime AS intaked_datetime
		,P.birth_date
		,SS.sample_status_name
        ,E.is_qced
        ,E.qced_at
        ,QC_U.first_name AS qc_by_first_name
        ,QC_U.last_name AS qc_by_last_name
	FROM
		tblSample T
		INNER JOIN tblSampleBatch SB ON T.sample_id = SB.sample_id
		LEFT OUTER JOIN tblUser U ON T.created_by = U.user_id
        LEFT OUTER JOIN tblEncounter E ON T.encounter_id = E.encounter_id
        LEFT OUTER JOIN tblUser QC_U ON E.qced_by = QC_U.user_id
        LEFT OUTER JOIN tblUser EU ON E.created_by = EU.user_id
		LEFT OUTER JOIN tblPatient P ON E.patient_id = P.patient_id
		LEFT OUTER JOIN tblContact P_C ON P.contact_id = P_C.contact_id
		LEFT OUTER JOIN tblDoctor D ON E.doctor_id = D.doctor_id
		LEFT OUTER JOIN tblContact D_C ON D.contact_id = D_C.contact_id
		LEFT OUTER JOIN tblSampleStatus SS ON T.sample_status_id = SS.sample_status_id
	WHERE
		SB.batch_id = s_batch_id
		AND
		SB.is_deleted = 0
	ORDER BY SB.sequence_num ASC;
END
EOT;
		$this->querySQL($sql);
	}

}
