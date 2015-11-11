<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20151111224907 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Test_Search`;

CREATE PROCEDURE `pr_Test_Search`(
    IN new_session VARCHAR(255),
    IN s_test_status_id SMALLINT UNSIGNED,
    IN s_patient_first_name VARCHAR(255),
    IN s_patient_last_name VARCHAR(255),
    IN s_ext_patient_id varchar(16),
    IN s_patient_dob DATE,
    IN s_account_name VARCHAR(255),
    IN s_account_num DECIMAL(4,0),
    IN s_ext_sample_id VARCHAR(45),
    IN s_is_repeat BOOLEAN,
    IN s_is_reswab BOOLEAN,
    IN s_is_nether_repeat_nor_reswab BOOLEAN,
    IN s_hide_summary BOOLEAN,
    IN s_timezone_shift BOOLEAN,
    IN s_claim_num VARCHAR(255),
    IN s_is_preintake_needed BOOLEAN,
    IN s_csr_lastname VARCHAR(255),
    IN s_doctor_last_name VARCHAR(255),
    IN s_ra_last_name VARCHAR(255),
    IN s_batch_id INT(11) UNSIGNED,
    IN s_report_date DATE,
    IN s_payer_type_id SMALLINT UNSIGNED,
    IN sort_field VARCHAR(255),
    IN sort_dir VARCHAR(255),
    IN page_start INT UNSIGNED,
    IN page_amount INT
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
            WHEN sort_dir = "DESC" THEN "DESC"
            ELSE "ASC"
        END
    INTO
        sort_dir;

    SELECT
        CASE
            WHEN sort_field = "account_name" THEN CONCAT("A_C.company_name", " ", sort_dir)
            ELSE NULL
        END
    INTO
        sort_field;

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
        SELECT SQL_CALC_FOUND_ROWS
            T.*,
            TS.test_status_name,
            TT.external_test_type_id,
            TT.is_summary,
            U.first_name AS created_by_first_name,
            U.last_name AS created_by_last_name,
            P.external_patient_id,
            P_C.first_name AS patient_first_name,
            P_C.last_name AS patient_last_name,
            P.birth_date,
            E.encounter_datetime,
            S.external_sample_id,
            S.received_datetime AS sample_received_datetime,
            S.is_preintake_needed,
            S.clinic_assistant_name,
            SB.batch_id,
            A_C.company_name AS account_name,
            D_C.first_name AS doctor_first_name,
            D_C.last_name AS doctor_last_name,
            PG.gender_abbreviation,
            P.race_bit,
            (
                SELECT GROUP_CONCAT(CASE WHEN P.race_bit & R.race_bit > 0 THEN R.race_abbreviation ELSE NULL END) FROM tblRace R
            ) AS race_abbreviation,
            PT.payer_type_id,
            PT.payer_type_name

            , (
                SELECT GROUP_CONCAT(ICD9.icd9 ORDER BY ICD9.icd9 SEPARATOR ', ')
                    FROM tblICD9 ICD9 
                    WHERE ICD9.encounter_id = E.encounter_id AND ICD9.is_deleted = 0
            ) AS icd9s
            FROM tblEncounter E
            INNER JOIN (
                SELECT encounter_id FROM tblEncounter E2 WHERE E2.is_deleted = 0 ORDER BY E2.created_datetime DESC
            ) AS E2 ON E.encounter_id = E2.encounter_id
             INNER JOIN tblSample S ON E.encounter_id = S.encounter_id AND E.is_deleted = 0
            LEFT JOIN tblSampleBatch SB ON S.sample_id = SB.sample_id AND SB.is_deleted = 0
            INNER JOIN tblTest T ON S.sample_id = T.sample_id AND S.is_deleted = 0 AND T.is_deleted = 0
            INNER JOIN tblTestType TT ON T.test_type_id = TT.test_type_id
            INNER JOIN tblTestStatus TS ON T.test_status_id = TS.test_status_id
            INNER JOIN tblUser U ON U.user_id = T.created_by
            INNER JOIN tblPatient P ON P.patient_id = E.patient_id AND P.is_deleted = 0
            INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0
            INNER JOIN tblAccount A ON A.account_id = E.account_id AND A.is_deleted = 0
            INNER JOIN tblContact A_C ON A.contact_id = A_C.contact_id AND A_C.is_deleted = 0
            INNER JOIN tblDoctor D ON D.doctor_id = E.doctor_id
            INNER JOIN tblContact D_C ON D.contact_id = D_C.contact_id
            INNER JOIN tblGender PG ON P.gender_id = PG.gender_id

            LEFT JOIN tblEncounterPayer EP ON EP.encounter_id = E.encounter_id AND EP.is_deleted = 0
            LEFT JOIN tblPayer PY ON EP.payer_id = PY.payer_id AND PY.is_deleted = 0
            LEFT JOIN tblPayerCompany PC ON PC.payer_company_id = PY.payer_company_id
            LEFT JOIN tblPayerType PT ON PT.payer_type_id = PC.payer_type_id

    "); 

    IF (s_csr_lastname IS NOT NULL AND LENGTH(s_csr_lastname) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            LEFT OUTER JOIN tblUser OWNER_USER ON A.sf_owner_user_id = OWNER_USER.sf_user_id");
    END IF;

    IF (s_ra_last_name IS NOT NULL AND LENGTH(s_ra_last_name) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            LEFT OUTER JOIN tblUser RA_USER ON A.sf_ra_user_id = RA_USER.sf_user_id");
    END IF;

    SET @query_sql := CONCAT(@query_sql,"
        WHERE
            (PY.coverage_order = 1 OR PY.coverage_order IS NULL)
    ");

    
    IF (s_test_status_id IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            T.test_status_id = ", s_test_status_id);
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

    
    IF (s_account_name IS NOT NULL AND LENGTH(s_account_name) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(A_C.company_name) LIKE '", LOWER(s_account_name), "%'");

        IF (sort_field IS NULL) THEN
            SELECT
                CASE
                    WHEN sort_field IS NULL THEN "A_C.company_name ASC"
                    ELSE sort_field
                END
            INTO
            sort_field;
        END IF;
    END IF;

    
    IF (s_account_num IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            A.account_num = ", s_account_num);
    END IF;

    
    IF (s_ext_sample_id IS NOT NULL AND LENGTH(s_ext_sample_id) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(S.external_sample_id) LIKE '", LOWER(s_ext_sample_id), "%'");
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

    IF (s_hide_summary IS NOT NULL AND s_hide_summary > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            TT.is_summary = 0");
    END IF;

    IF (s_timezone_shift IS NOT NULL AND s_timezone_shift > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            T.report_pdf_created_at < CONVERT_TZ(NOW(),'+00:00','-08:00')");
    END IF;

    
    IF (s_claim_num IS NOT NULL AND LENGTH(s_claim_num) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(T.claim_num) LIKE '", LOWER(s_claim_num), "%'");
    END IF;

    
    IF (s_is_preintake_needed IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            S.is_preintake_needed = ", s_is_preintake_needed);
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
    
    IF (s_batch_id IS NOT NULL AND s_batch_id > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            (
                SB.batch_id  = ",s_batch_id,"
            )");
    END IF;

    IF (s_report_date IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            CAST(T.report_pdf_created_at AS DATE) = CAST('", s_report_date, "' AS DATE)");
    END IF;

    IF (s_payer_type_id IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            PC.payer_type_id = ", s_payer_type_id);
    END IF;

    IF (sort_field IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            ORDER BY ", sort_field);
    END IF;

    IF (page_amount > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            LIMIT ", page_start,", ", page_amount);
    END IF;

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
DROP PROCEDURE IF EXISTS `pr_Test_Search`;

CREATE PROCEDURE `pr_Test_Search`(
    IN new_session VARCHAR(255),
    IN s_test_status_id SMALLINT UNSIGNED,
    IN s_patient_first_name VARCHAR(255),
    IN s_patient_last_name VARCHAR(255),
    IN s_ext_patient_id varchar(16),
    IN s_patient_dob DATE,
    IN s_account_name VARCHAR(255),
    IN s_account_num DECIMAL(4,0),
    IN s_ext_sample_id VARCHAR(45),
    IN s_is_repeat BOOLEAN,
    IN s_is_reswab BOOLEAN,
    IN s_is_nether_repeat_nor_reswab BOOLEAN,
    IN s_hide_summary BOOLEAN,
    IN s_claim_num VARCHAR(255),
    IN s_is_preintake_needed BOOLEAN,
    IN s_csr_lastname VARCHAR(255),
    IN s_doctor_last_name VARCHAR(255),
    IN s_ra_last_name VARCHAR(255),
    IN s_batch_id INT(11) UNSIGNED,
    IN s_report_date DATE,
    IN s_payer_type_id SMALLINT UNSIGNED,
    IN sort_field VARCHAR(255),
    IN sort_dir VARCHAR(255),
    IN page_start INT UNSIGNED,
    IN page_amount INT
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
            WHEN sort_dir = "DESC" THEN "DESC"
            ELSE "ASC"
        END
    INTO
        sort_dir;

    SELECT
        CASE
            WHEN sort_field = "account_name" THEN CONCAT("A_C.company_name", " ", sort_dir)
            ELSE NULL
        END
    INTO
        sort_field;

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
        SELECT SQL_CALC_FOUND_ROWS
            T.*,
            TS.test_status_name,
            TT.external_test_type_id,
            TT.is_summary,
            U.first_name AS created_by_first_name,
            U.last_name AS created_by_last_name,
            P.external_patient_id,
            P_C.first_name AS patient_first_name,
            P_C.last_name AS patient_last_name,
            P.birth_date,
            E.encounter_datetime,
            S.external_sample_id,
            S.received_datetime AS sample_received_datetime,
            S.is_preintake_needed,
            S.clinic_assistant_name,
            SB.batch_id,
            A_C.company_name AS account_name,
            D_C.first_name AS doctor_first_name,
            D_C.last_name AS doctor_last_name,
            PG.gender_abbreviation,
            P.race_bit,
            (
				SELECT GROUP_CONCAT(CASE WHEN P.race_bit & R.race_bit > 0 THEN R.race_abbreviation ELSE NULL END) FROM tblRace R
			) AS race_abbreviation,
            PT.payer_type_id,
            PT.payer_type_name

			, (
				SELECT GROUP_CONCAT(ICD9.icd9 ORDER BY ICD9.icd9 SEPARATOR ', ')
					FROM tblICD9 ICD9 
					WHERE ICD9.encounter_id = E.encounter_id AND ICD9.is_deleted = 0
            ) AS icd9s
            FROM tblEncounter E
            INNER JOIN (
                SELECT encounter_id FROM tblEncounter E2 WHERE E2.is_deleted = 0 ORDER BY E2.created_datetime DESC
            ) AS E2 ON E.encounter_id = E2.encounter_id
             INNER JOIN tblSample S ON E.encounter_id = S.encounter_id AND E.is_deleted = 0
            LEFT JOIN tblSampleBatch SB ON S.sample_id = SB.sample_id AND SB.is_deleted = 0
            INNER JOIN tblTest T ON S.sample_id = T.sample_id AND S.is_deleted = 0 AND T.is_deleted = 0
            INNER JOIN tblTestType TT ON T.test_type_id = TT.test_type_id
            INNER JOIN tblTestStatus TS ON T.test_status_id = TS.test_status_id
            INNER JOIN tblUser U ON U.user_id = T.created_by
            INNER JOIN tblPatient P ON P.patient_id = E.patient_id AND P.is_deleted = 0
            INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0
            INNER JOIN tblAccount A ON A.account_id = E.account_id AND A.is_deleted = 0
            INNER JOIN tblContact A_C ON A.contact_id = A_C.contact_id AND A_C.is_deleted = 0
            INNER JOIN tblDoctor D ON D.doctor_id = E.doctor_id
            INNER JOIN tblContact D_C ON D.contact_id = D_C.contact_id
            INNER JOIN tblGender PG ON P.gender_id = PG.gender_id

			LEFT JOIN tblEncounterPayer EP ON EP.encounter_id = E.encounter_id AND EP.is_deleted = 0
			LEFT JOIN tblPayer PY ON EP.payer_id = PY.payer_id AND PY.is_deleted = 0
			LEFT JOIN tblPayerCompany PC ON PC.payer_company_id = PY.payer_company_id
 			LEFT JOIN tblPayerType PT ON PT.payer_type_id = PC.payer_type_id

    "); 

    IF (s_csr_lastname IS NOT NULL AND LENGTH(s_csr_lastname) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            LEFT OUTER JOIN tblUser OWNER_USER ON A.sf_owner_user_id = OWNER_USER.sf_user_id");
    END IF;

    IF (s_ra_last_name IS NOT NULL AND LENGTH(s_ra_last_name) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            LEFT OUTER JOIN tblUser RA_USER ON A.sf_ra_user_id = RA_USER.sf_user_id");
    END IF;

    SET @query_sql := CONCAT(@query_sql,"
        WHERE
			(PY.coverage_order = 1 OR PY.coverage_order IS NULL)
    ");

    
    IF (s_test_status_id IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            T.test_status_id = ", s_test_status_id);
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

    
    IF (s_account_name IS NOT NULL AND LENGTH(s_account_name) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(A_C.company_name) LIKE '", LOWER(s_account_name), "%'");

        IF (sort_field IS NULL) THEN
            SELECT
                CASE
                    WHEN sort_field IS NULL THEN "A_C.company_name ASC"
                    ELSE sort_field
                END
            INTO
            sort_field;
        END IF;
    END IF;

    
    IF (s_account_num IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            A.account_num = ", s_account_num);
    END IF;

    
    IF (s_ext_sample_id IS NOT NULL AND LENGTH(s_ext_sample_id) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(S.external_sample_id) LIKE '", LOWER(s_ext_sample_id), "%'");
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

    IF (s_hide_summary IS NOT NULL AND s_hide_summary > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            TT.is_summary = 0");
    END IF;

    
    IF (s_claim_num IS NOT NULL AND LENGTH(s_claim_num) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(T.claim_num) LIKE '", LOWER(s_claim_num), "%'");
    END IF;

    
    IF (s_is_preintake_needed IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            S.is_preintake_needed = ", s_is_preintake_needed);
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
    
    IF (s_batch_id IS NOT NULL AND s_batch_id > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            (
                SB.batch_id  = ",s_batch_id,"
            )");
    END IF;

    IF (s_report_date IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            CAST(T.report_pdf_created_at AS DATE) = CAST('", s_report_date, "' AS DATE)");
    END IF;

	IF (s_payer_type_id IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            PC.payer_type_id = ", s_payer_type_id);
    END IF;

    IF (sort_field IS NOT NULL) THEN
        SET @query_sql := CONCAT(@query_sql,"
            ORDER BY ", sort_field);
    END IF;

	IF (page_amount > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			LIMIT ", page_start,", ", page_amount);
	END IF;

    PREPARE search_query FROM @query_sql;
    EXECUTE search_query;

    SELECT FOUND_ROWS() AS total_rows;

    DEALLOCATE PREPARE search_query;
END
EOT;
		$this->querySQL($sql);
	}

}
