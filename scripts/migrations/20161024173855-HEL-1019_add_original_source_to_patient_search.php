<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161024173855 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Patient_GetByEncounter`;

CREATE PROCEDURE `pr_Patient_GetByEncounter`(
	IN new_session VARCHAR(255),
	IN s_encounter_id  INT UNSIGNED
)
this:BEGIN
	DECLARE c_id INT UNSIGNED;
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
		T.patient_id
,T.contact_id
,T.gender_id
,T.race_bit
,T.birth_date
,T.social_security_num
,T.attorney_contact_id
,T.external_patient_id
,C.contact_id
,C.contact_type_id
,C.is_company
,C.company_name
,C.title
,C.prefix
,C.first_name
,C.middle_name
,C.last_name
,C.suffix
,C.email
,T.original_source
	FROM
		tblPatient T
		INNER JOIN tblContact C ON T.contact_id = C.contact_id
		INNER JOIN tblEncounter E ON T.patient_id = E.patient_id
	WHERE
		E.encounter_id = s_encounter_id
		AND
		E.is_deleted = 0
		AND
		T.is_deleted = 0
		AND
		C.is_deleted = 0;

SET c_id := (
SELECT
	 C.contact_id
FROM
		tblPatient T
		INNER JOIN tblContact C ON T.contact_id = C.contact_id
		INNER JOIN tblEncounter E ON T.patient_id = E.patient_id
	WHERE
		E.encounter_id = s_encounter_id
		AND
		E.is_deleted = 0
		AND
		T.is_deleted = 0
		AND
		C.is_deleted = 0);

	SELECT * FROM tblPhone WHERE contact_id = c_id AND is_deleted = 0;
	SELECT * FROM tblAddress WHERE contact_id = c_id AND is_deleted = 0;


END
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Patient_GetByEncounter`;

CREATE DEFINER=`root`@`%` PROCEDURE `pr_Patient_GetByEncounter`(
	IN new_session VARCHAR(255),
	IN s_encounter_id  INT UNSIGNED
)
this:BEGIN
	DECLARE c_id INT UNSIGNED;
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
		T.patient_id
,T.contact_id
,T.gender_id
,T.race_bit
,T.birth_date
,T.social_security_num
,T.attorney_contact_id
,T.external_patient_id
,C.contact_id
,C.contact_type_id
,C.is_company
,C.company_name
,C.title
,C.prefix
,C.first_name
,C.middle_name
,C.last_name
,C.suffix
,C.email
	FROM
		tblPatient T
		INNER JOIN tblContact C ON T.contact_id = C.contact_id
		INNER JOIN tblEncounter E ON T.patient_id = E.patient_id
	WHERE
		E.encounter_id = s_encounter_id
		AND
		E.is_deleted = 0
		AND
		T.is_deleted = 0
		AND
		C.is_deleted = 0;

SET c_id := (
SELECT
	 C.contact_id
FROM
		tblPatient T
		INNER JOIN tblContact C ON T.contact_id = C.contact_id
		INNER JOIN tblEncounter E ON T.patient_id = E.patient_id
	WHERE
		E.encounter_id = s_encounter_id
		AND
		E.is_deleted = 0
		AND
		T.is_deleted = 0
		AND
		C.is_deleted = 0);

	SELECT * FROM tblPhone WHERE contact_id = c_id AND is_deleted = 0;
	SELECT * FROM tblAddress WHERE contact_id = c_id AND is_deleted = 0;


END
EOT;
		$this->querySQL($sql);
	}

}
