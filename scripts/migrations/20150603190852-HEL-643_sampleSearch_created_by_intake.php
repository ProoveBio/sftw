<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150603190852 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Sample_Search`;

CREATE PROCEDURE `pr_Sample_Search`(
	IN new_session VARCHAR(255),
	IN s_sample_status_id SMALLINT,
  IN s_patient_first_name VARCHAR(255),
  IN s_patient_last_name VARCHAR(255),
  IN s_ext_patient_id VARCHAR(255),
  IN s_patient_dob DATE,
  IN s_ext_sample_id VARCHAR(255),
  IN s_batch_id VARCHAR(255),
	IN s_is_repeat BOOLEAN,
	IN s_is_reswab BOOLEAN,
	IN s_is_nether_repeat_nor_reswab BOOLEAN,
	IN s_is_preintake_needed BOOLEAN,
	IN s_account_name VARCHAR(255),
	IN s_csr_lastname VARCHAR(255),
  IN s_doctor_last_name VARCHAR(255),
  IN s_ra_last_name VARCHAR(255),
  IN s_created_by INT UNSIGNED,
	IN sort_field VARCHAR(255),
	IN sort_dir VARCHAR(255),
	IN page_start INT UNSIGNED,
	IN page_amount INT UNSIGNED
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
		CASE
			WHEN sort_field IS NULL THEN "S.received_datetime"
			WHEN sort_field = "received_datetime" THEN "S.received_datetime"
			WHEN sort_field = "sample_id" THEN "S.external_sample_id"
			WHEN sort_field = "sample_status_id" THEN "S.sample_status_id"
			ELSE "S.received_datetime"
		END
	INTO
		sort_field;

	SELECT
		CASE
			WHEN sort_dir = "DESC" THEN "DESC"
			ELSE "ASC"
		END
	INTO
		sort_dir;

	SELECT
		CASE
			WHEN page_start IS NULL THEN 0
			ELSE page_start
		END
	INTO
		page_start;

	SELECT
		CASE
			WHEN page_amount IS NULL THEN 10
			ELSE page_amount
		END
	INTO
		page_amount;

	
	SET @query_sql := CONCAT("
		SELECT DISTINCT SQL_CALC_FOUND_ROWS
			 S.sample_id
			,S.sample_status_id
			,S.encounter_id
            ,E.is_qced
			,S.clinic_assistant_name
			,S.external_sample_id
			,S.received_datetime
			,S.result_datetime
			,S.is_reswab_needed
			,S.reswab_requested_notes
			,S.reswab_requested_datetime
			,S.is_repeat_needed
			,S.repeat_result_datetime
			,S.is_preintake_needed
			,S.is_deleted
			,E.created_datetime
			,E.created_by
			,S.modified_datetime
			,S.modified_by
			,SS.sample_status_name
			,U.first_name AS created_by_first_name
			,U.last_name AS created_by_last_name
			,GROUP_CONCAT(DISTINCT SB.batch_id ORDER BY SB.created_datetime DESC SEPARATOR ',') AS batch_ids
			,D_C.first_name AS doctor_first_name
			,D_C.last_name AS doctor_last_name
			,P_C.first_name AS patient_first_name
			,P_C.last_name AS patient_last_name
			,P.birth_date
			,P.external_patient_id
			,A_C.company_name AS account_name
			,GROUP_CONCAT(DISTINCT TT.external_test_type_id SEPARATOR ', ') AS failed_tests
		FROM
			tblSample S
                        INNER JOIN tblSampleStatus SS ON SS.sample_status_id = S.sample_status_id
			INNER JOIN tblEncounter E ON S.encounter_id = E.encounter_id AND E.is_deleted = 0
			INNER JOIN tblUser U ON U.user_id = E.created_by
			INNER JOIN tblAccount A ON A.account_id = E.account_id AND A.is_deleted = 0
			INNER JOIN tblContact A_C ON A.contact_id = A_C.contact_id AND A_C.is_deleted = 0
      			INNER JOIN tblPatient P ON P.patient_id = E.patient_id AND P.is_deleted = 0
     			INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0
			INNER JOIN tblDoctor D ON D.doctor_id = E.doctor_id AND D.is_deleted = 0
     			INNER JOIN tblContact D_C ON D.contact_id = D_C.contact_id AND D_C.is_deleted = 0
			LEFT OUTER JOIN tblUser OWNER_USER ON A.sf_owner_user_id = OWNER_USER.sf_user_id
      			LEFT OUTER JOIN tblUser RA_USER ON A.sf_ra_user_id = RA_USER.sf_user_id
			LEFT OUTER JOIN tblSampleBatch SB ON SB.sample_id = S.sample_id AND SB.is_deleted = 0
			LEFT OUTER JOIN tblTest FAILED_TESTS ON S.sample_id = FAILED_TESTS.sample_ID AND FAILED_TESTS.test_status_id = 7 AND FAILED_TESTS.is_deleted = 0
			LEFT OUTER JOIN tblTestType TT ON FAILED_TESTS.test_type_id = TT.test_type_id
		WHERE
			S.is_deleted = 0");


	
	IF (s_sample_status_id IS NOT NULL) THEN
		IF (s_sample_status_id < 0) THEN
			SET @query_sql := CONCAT(@query_sql,"
				AND
				S.sample_status_id <= ABS(", s_sample_status_id, ")");
        ELSE
			SET @query_sql := CONCAT(@query_sql,"
				AND
				S.sample_status_id = ", s_sample_status_id);
        END IF;
	END IF;

  
  IF (s_patient_first_name IS NOT NULL AND LENGTH(s_patient_first_name) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(P_C.first_name) LIKE '", LOWER(s_patient_first_name), "%'");
    END IF;

  
  IF (s_patient_last_name IS NOT NULL AND LENGTH(s_patient_last_name) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(P_C.last_name) LIKE '", LOWER(s_patient_last_name), "%'");
    END IF;

  
  IF (s_ext_patient_id IS NOT NULL AND LENGTH(s_ext_patient_id) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(P.external_patient_id) LIKE '", LOWER(s_ext_patient_id), "%'");
  END IF;

  
  IF (s_patient_dob IS NOT NULL) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    P.birth_date = CAST('", s_patient_dob, "' AS DATE)");
  END IF;

  
  IF (s_ext_sample_id IS NOT NULL AND LENGTH(s_ext_sample_id) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(S.external_sample_id) LIKE '", LOWER(s_ext_sample_id), "%'");
  END IF;

  
  IF (s_batch_id IS NOT NULL AND LENGTH(s_batch_id) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    SB.batch_id = ", s_batch_id);
  END IF;

	
	IF (s_is_repeat IS NOT NULL AND s_is_repeat > 0 AND (s_is_reswab IS NULL OR s_is_reswab = 0)) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.is_repeat_needed = 1
			AND
			S.is_reswab_needed = 0");
	END IF;

	
	IF (s_is_reswab IS NOT NULL AND s_is_reswab > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.is_reswab_needed = 1");
	END IF;

	
	IF (s_is_nether_repeat_nor_reswab IS NOT NULL AND s_is_nether_repeat_nor_reswab > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.is_reswab_needed = 0
			AND
			S.is_repeat_needed = 0");
	END IF;

	IF (s_is_preintake_needed IS NOT NULL) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.is_preintake_needed = ", s_is_preintake_needed);
	END IF;

	
	IF (s_account_name IS NOT NULL AND LENGTH(s_account_name) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			LOWER(A_C.company_name) LIKE '", LOWER(s_account_name), "%'");
	END IF;


	
	IF (s_csr_lastname IS NOT NULL AND LENGTH(s_csr_lastname) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			LOWER(OWNER_USER.last_name) LIKE '", LOWER(s_csr_lastname), "%'");
	END IF;

  
  IF (s_doctor_last_name IS NOT NULL AND LENGTH(s_doctor_last_name) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(D_C.last_name) LIKE '", LOWER(s_doctor_last_name), "%'");
  END IF;

  
  IF (s_ra_last_name IS NOT NULL AND LENGTH(s_ra_last_name) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    (
      LOWER(RA_USER.last_name) LIKE '", LOWER(s_ra_last_name), "%'
      OR
      LOWER(A.clinical_associate) LIKE '% ", LOWER(s_ra_last_name), "%'
    )");
  END IF;

	
	IF (s_created_by IS NOT NULL AND s_created_by > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			E.created_by = '", s_created_by, "'");
	END IF;

	SET @query_sql := CONCAT(@query_sql,"
		GROUP BY S.sample_id");

	IF (s_sample_status_id = 1) THEN
	SET @query_sql := CONCAT(@query_sql,"
		ORDER BY S.is_repeat_needed DESC, DATE(S.created_datetime) ASC, A.account_id, D.doctor_id");
	ELSE
	SET @query_sql := CONCAT(@query_sql,"
		ORDER BY ", sort_field," ", sort_dir);
	END IF;

	SET @query_sql := CONCAT(@query_sql,"
		LIMIT ", page_start,", ", page_amount);

 	PREPARE search_query FROM @query_sql;
 	EXECUTE search_query;

 	SELECT FOUND_ROWS() AS total_rows;

    DEALLOCATE PREPARE search_query;
END
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Sample_Search`;

CREATE PROCEDURE `pr_Sample_Search`(
	IN new_session VARCHAR(255),
	IN s_sample_status_id SMALLINT,
  IN s_patient_first_name VARCHAR(255),
  IN s_patient_last_name VARCHAR(255),
  IN s_ext_patient_id VARCHAR(255),
  IN s_patient_dob DATE,
  IN s_ext_sample_id VARCHAR(255),
  IN s_batch_id VARCHAR(255),
	IN s_is_repeat BOOLEAN,
	IN s_is_reswab BOOLEAN,
	IN s_is_nether_repeat_nor_reswab BOOLEAN,
	IN s_is_preintake_needed BOOLEAN,
	IN s_account_name VARCHAR(255),
	IN s_csr_lastname VARCHAR(255),
  IN s_doctor_last_name VARCHAR(255),
  IN s_ra_last_name VARCHAR(255),
  IN s_created_by INT UNSIGNED,
	IN sort_field VARCHAR(255),
	IN sort_dir VARCHAR(255),
	IN page_start INT UNSIGNED,
	IN page_amount INT UNSIGNED
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
		CASE
			WHEN sort_field IS NULL THEN "S.received_datetime"
			WHEN sort_field = "received_datetime" THEN "S.received_datetime"
			WHEN sort_field = "sample_id" THEN "S.external_sample_id"
			WHEN sort_field = "sample_status_id" THEN "S.sample_status_id"
			ELSE "S.received_datetime"
		END
	INTO
		sort_field;

	SELECT
		CASE
			WHEN sort_dir = "DESC" THEN "DESC"
			ELSE "ASC"
		END
	INTO
		sort_dir;

	SELECT
		CASE
			WHEN page_start IS NULL THEN 0
			ELSE page_start
		END
	INTO
		page_start;

	SELECT
		CASE
			WHEN page_amount IS NULL THEN 10
			ELSE page_amount
		END
	INTO
		page_amount;

	
	SET @query_sql := CONCAT("
		SELECT DISTINCT SQL_CALC_FOUND_ROWS
			 S.sample_id
			,S.sample_status_id
			,S.encounter_id
            ,E.is_qced
			,S.clinic_assistant_name
			,S.external_sample_id
			,S.received_datetime
			,S.result_datetime
			,S.is_reswab_needed
			,S.reswab_requested_notes
			,S.reswab_requested_datetime
			,S.is_repeat_needed
			,S.repeat_result_datetime
			,S.is_preintake_needed
			,S.is_deleted
			,S.created_datetime
			,S.created_by
			,S.modified_datetime
			,S.modified_by
			,SS.sample_status_name
			,U.first_name AS created_by_first_name
			,U.last_name AS created_by_last_name
			,GROUP_CONCAT(DISTINCT SB.batch_id ORDER BY SB.created_datetime DESC SEPARATOR ',') AS batch_ids
			,D_C.first_name AS doctor_first_name
			,D_C.last_name AS doctor_last_name
			,P_C.first_name AS patient_first_name
			,P_C.last_name AS patient_last_name
			,P.birth_date
			,P.external_patient_id
			,A_C.company_name AS account_name
			,GROUP_CONCAT(DISTINCT TT.external_test_type_id SEPARATOR ', ') AS failed_tests
		FROM
			tblSample S
                        INNER JOIN tblSampleStatus SS ON SS.sample_status_id = S.sample_status_id
			INNER JOIN tblUser U ON U.user_id = S.created_by
			INNER JOIN tblEncounter E ON S.encounter_id = E.encounter_id AND E.is_deleted = 0
			INNER JOIN tblAccount A ON A.account_id = E.account_id AND A.is_deleted = 0
			INNER JOIN tblContact A_C ON A.contact_id = A_C.contact_id AND A_C.is_deleted = 0
      			INNER JOIN tblPatient P ON P.patient_id = E.patient_id AND P.is_deleted = 0
     			INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0
			INNER JOIN tblDoctor D ON D.doctor_id = E.doctor_id AND D.is_deleted = 0
     			INNER JOIN tblContact D_C ON D.contact_id = D_C.contact_id AND D_C.is_deleted = 0
			LEFT OUTER JOIN tblUser OWNER_USER ON A.sf_owner_user_id = OWNER_USER.sf_user_id
      			LEFT OUTER JOIN tblUser RA_USER ON A.sf_ra_user_id = RA_USER.sf_user_id
			LEFT OUTER JOIN tblSampleBatch SB ON SB.sample_id = S.sample_id AND SB.is_deleted = 0
			LEFT OUTER JOIN tblTest FAILED_TESTS ON S.sample_id = FAILED_TESTS.sample_ID AND FAILED_TESTS.test_status_id = 7 AND FAILED_TESTS.is_deleted = 0
			LEFT OUTER JOIN tblTestType TT ON FAILED_TESTS.test_type_id = TT.test_type_id
		WHERE
			S.is_deleted = 0");


	
	IF (s_sample_status_id IS NOT NULL) THEN
		IF (s_sample_status_id < 0) THEN
			SET @query_sql := CONCAT(@query_sql,"
				AND
				S.sample_status_id <= ABS(", s_sample_status_id, ")");
        ELSE
			SET @query_sql := CONCAT(@query_sql,"
				AND
				S.sample_status_id = ", s_sample_status_id);
        END IF;
	END IF;

  
  IF (s_patient_first_name IS NOT NULL AND LENGTH(s_patient_first_name) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(P_C.first_name) LIKE '", LOWER(s_patient_first_name), "%'");
    END IF;

  
  IF (s_patient_last_name IS NOT NULL AND LENGTH(s_patient_last_name) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(P_C.last_name) LIKE '", LOWER(s_patient_last_name), "%'");
    END IF;

  
  IF (s_ext_patient_id IS NOT NULL AND LENGTH(s_ext_patient_id) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(P.external_patient_id) LIKE '", LOWER(s_ext_patient_id), "%'");
  END IF;

  
  IF (s_patient_dob IS NOT NULL) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    P.birth_date = CAST('", s_patient_dob, "' AS DATE)");
  END IF;

  
  IF (s_ext_sample_id IS NOT NULL AND LENGTH(s_ext_sample_id) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(S.external_sample_id) LIKE '", LOWER(s_ext_sample_id), "%'");
  END IF;

  
  IF (s_batch_id IS NOT NULL AND LENGTH(s_batch_id) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    SB.batch_id = ", s_batch_id);
  END IF;

	
	IF (s_is_repeat IS NOT NULL AND s_is_repeat > 0 AND (s_is_reswab IS NULL OR s_is_reswab = 0)) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.is_repeat_needed = 1
			AND
			S.is_reswab_needed = 0");
	END IF;

	
	IF (s_is_reswab IS NOT NULL AND s_is_reswab > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.is_reswab_needed = 1");
	END IF;

	
	IF (s_is_nether_repeat_nor_reswab IS NOT NULL AND s_is_nether_repeat_nor_reswab > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.is_reswab_needed = 0
			AND
			S.is_repeat_needed = 0");
	END IF;

	IF (s_is_preintake_needed IS NOT NULL) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.is_preintake_needed = ", s_is_preintake_needed);
	END IF;

	
	IF (s_account_name IS NOT NULL AND LENGTH(s_account_name) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			LOWER(A_C.company_name) LIKE '", LOWER(s_account_name), "%'");
	END IF;


	
	IF (s_csr_lastname IS NOT NULL AND LENGTH(s_csr_lastname) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			LOWER(OWNER_USER.last_name) LIKE '", LOWER(s_csr_lastname), "%'");
	END IF;

  
  IF (s_doctor_last_name IS NOT NULL AND LENGTH(s_doctor_last_name) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    LOWER(D_C.last_name) LIKE '", LOWER(s_doctor_last_name), "%'");
  END IF;

  
  IF (s_ra_last_name IS NOT NULL AND LENGTH(s_ra_last_name) > 0) THEN
  SET @query_sql := CONCAT(@query_sql,"
    AND
    (
      LOWER(RA_USER.last_name) LIKE '", LOWER(s_ra_last_name), "%'
      OR
      LOWER(A.clinical_associate) LIKE '% ", LOWER(s_ra_last_name), "%'
    )");
  END IF;

	
	IF (s_created_by IS NOT NULL AND s_created_by > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			S.created_by = '", s_created_by, "'");
	END IF;

	SET @query_sql := CONCAT(@query_sql,"
		GROUP BY S.sample_id");

	IF (s_sample_status_id = 1) THEN
	SET @query_sql := CONCAT(@query_sql,"
		ORDER BY S.is_repeat_needed DESC, DATE(S.created_datetime) ASC, A.account_id, D.doctor_id");
	ELSE
	SET @query_sql := CONCAT(@query_sql,"
		ORDER BY ", sort_field," ", sort_dir);
	END IF;

	SET @query_sql := CONCAT(@query_sql,"
		LIMIT ", page_start,", ", page_amount);

 	PREPARE search_query FROM @query_sql;
 	EXECUTE search_query;

 	SELECT FOUND_ROWS() AS total_rows;

    DEALLOCATE PREPARE search_query;
END
EOT;
		$this->querySQL($sql);
	}

}
