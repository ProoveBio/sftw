<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161017213543 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Encounter_Search`;

CREATE PROCEDURE `pr_Encounter_Search`(
    IN new_session VARCHAR(255),
    IN s_pat_lname  VARCHAR(255),
    IN s_pat_fname VARCHAR(255),
    IN s_protocol VARCHAR(255),
    IN s_ext_patient_id VARCHAR(255),
    IN s_patient_dob DATE,
    IN s_account_name VARCHAR(255),
    IN s_ext_sample_id VARCHAR(255),
    IN s_csr_lastname VARCHAR(255),
    IN s_doctor_last_name VARCHAR(255),
    IN s_ra_last_name VARCHAR(255),
    IN s_is_preintake_needed VARCHAR(255),
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
            WHEN sort_field IS NULL THEN "E.created_datetime"
            WHEN sort_field = "first_name" THEN "C.first_name"
            WHEN sort_field = "last_name" THEN "C.last_name"
            WHEN sort_field = "protocol" THEN "TEST.protocol"
            WHEN sort_field = "modified_datetime" THEN "E.modified_datetime"
            WHEN sort_field = "created_datetime" THEN "E.created_datetime"
            WHEN sort_field = "encounter_datetime" THEN "E.encounter_datetime"
            ELSE "E.created_datetime"
        END
    INTO
        sort_field;

    SELECT
        CASE
            WHEN sort_dir = "ASC" THEN "ASC"
            ELSE "DESC"
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
             P_C.first_name
            ,P_C.last_name
            ,P.birth_date
            ,TEST.protocol
            ,E.encounter_id
            ,E.encounter_datetime
            ,S.external_sample_id
            ,E.created_datetime AS created_datetime
            ,S_U.first_name AS created_by_first_name
            ,S_U.last_name AS created_by_last_name
            ,SS.sample_status_name
            ,D_C.first_name AS doctor_first_name
            ,D_C.last_name AS doctor_last_name
            ,OWNER_USER.first_name AS supervisor_first_name
            ,OWNER_USER.last_name AS supervisor_last_name
            ,S.clinic_assistant_name
            ,SB.batch_id
        FROM
            tblPatient P
            INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0
            INNER JOIN tblEncounter E ON P.patient_id = E.patient_id AND E.is_deleted = 0
            INNER JOIN tblSample S ON S.encounter_id = E.encounter_id AND S.is_deleted = 0
            INNER JOIN tblSampleStatus SS ON S.sample_status_id = SS.sample_status_id
            INNER JOIN tblAccount A ON A.account_id = E.account_id AND A.is_deleted = 0
            INNER JOIN tblContact A_C ON A.contact_id = A_C.contact_id AND A_C.is_deleted = 0
            INNER JOIN tblTest TEST ON TEST.encounter_id = E.encounter_id AND TEST.is_deleted = 0
            INNER JOIN tblDoctor D ON D.doctor_id = E.doctor_id AND D.is_deleted = 0
            INNER JOIN tblContact D_C ON D.contact_id = D_C.contact_id AND D_C.is_deleted = 0
            LEFT OUTER JOIN tblSampleBatch SB ON SB.sample_id = S.sample_id AND SB.is_deleted = 0
            LEFT OUTER JOIN tblUser OWNER_USER ON A.sf_owner_user_id = OWNER_USER.sf_user_id
            -- LEFT OUTER JOIN tblUser RA_USER ON A.sf_ra_user_id = RA_USER.sf_user_id
            LEFT OUTER JOIN tblUser S_U ON E.created_by = S_U.user_id
        WHERE
            P.is_deleted = 0
            AND
            P_C.contact_type_id = 5");

    IF (s_pat_lname IS NOT NULL AND LENGTH(s_pat_lname) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(P_C.last_name) LIKE '", LOWER(s_pat_lname), "%'");
    END IF;

    IF (s_pat_fname IS NOT NULL AND LENGTH(s_pat_fname) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(P_C.first_name) LIKE '", LOWER(s_pat_fname), "%'");
    END IF;

    IF (s_protocol IS NOT NULL AND LENGTH(s_protocol) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(TEST.protocol) LIKE '", LOWER(s_protocol), "%'");
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
    END IF;

    
    IF (s_ext_sample_id IS NOT NULL AND LENGTH(s_ext_sample_id) > 0) THEN
    SET @query_sql := CONCAT(@query_sql,"
        AND
        LOWER(S.external_sample_id) LIKE '", LOWER(s_ext_sample_id), "%'");
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
        LOWER(S.clinic_assistant_name) LIKE '%", LOWER(s_ra_last_name), "%'
        -- (
        --     LOWER(RA_USER.last_name) LIKE '", LOWER(s_ra_last_name), "%'
        --     OR
        --     LOWER(A.clinical_associate) LIKE '% ", LOWER(s_ra_last_name), "%'
        -- )
        ");
    END IF;

    
    IF (s_is_preintake_needed IS NOT NULL) THEN
    SET @query_sql := CONCAT(@query_sql,"
        AND
        S.is_preintake_needed = ", s_is_preintake_needed, " AND A.parent_id NOT IN (17,364)"); -- Exclude Proove accounts
    END IF;

    SET @query_sql := CONCAT(@query_sql,"
        ORDER BY ", sort_field," ", sort_dir,"
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
DROP PROCEDURE IF EXISTS `pr_Encounter_Search`;

CREATE PROCEDURE `pr_Encounter_Search`(
    IN new_session VARCHAR(255),
    IN s_pat_lname  VARCHAR(255),
    IN s_pat_fname VARCHAR(255),
    IN s_protocol VARCHAR(255),
    IN s_ext_patient_id VARCHAR(255),
    IN s_patient_dob DATE,
    IN s_account_name VARCHAR(255),
    IN s_ext_sample_id VARCHAR(255),
    IN s_csr_lastname VARCHAR(255),
    IN s_doctor_last_name VARCHAR(255),
    IN s_ra_last_name VARCHAR(255),
    IN s_is_preintake_needed VARCHAR(255),
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
            WHEN sort_field IS NULL THEN "E.created_datetime"
            WHEN sort_field = "first_name" THEN "C.first_name"
            WHEN sort_field = "last_name" THEN "C.last_name"
            WHEN sort_field = "protocol" THEN "TEST.protocol"
            WHEN sort_field = "modified_datetime" THEN "E.modified_datetime"
            WHEN sort_field = "created_datetime" THEN "E.created_datetime"
            WHEN sort_field = "encounter_datetime" THEN "E.encounter_datetime"
            ELSE "E.created_datetime"
        END
    INTO
        sort_field;

    SELECT
        CASE
            WHEN sort_dir = "ASC" THEN "ASC"
            ELSE "DESC"
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
             P_C.first_name
            ,P_C.last_name
            ,P.birth_date
            ,TEST.protocol
            ,E.encounter_id
            ,E.encounter_datetime
            ,S.external_sample_id
            ,E.created_datetime AS created_datetime
            ,S_U.first_name AS created_by_first_name
            ,S_U.last_name AS created_by_last_name
            ,SS.sample_status_name
            ,D_C.first_name AS doctor_first_name
            ,D_C.last_name AS doctor_last_name
            ,OWNER_USER.first_name AS supervisor_first_name
            ,OWNER_USER.last_name AS supervisor_last_name
            ,S.clinic_assistant_name
            ,SB.batch_id
        FROM
            tblPatient P
            INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0
            INNER JOIN tblEncounter E ON P.patient_id = E.patient_id AND E.is_deleted = 0
            INNER JOIN tblSample S ON S.encounter_id = E.encounter_id AND S.is_deleted = 0
            INNER JOIN tblSampleStatus SS ON S.sample_status_id = SS.sample_status_id
            INNER JOIN tblAccount A ON A.account_id = E.account_id AND A.is_deleted = 0
            INNER JOIN tblContact A_C ON A.contact_id = A_C.contact_id AND A_C.is_deleted = 0
            INNER JOIN tblTest TEST ON TEST.encounter_id = E.encounter_id AND TEST.is_deleted = 0
            INNER JOIN tblDoctor D ON D.doctor_id = E.doctor_id AND D.is_deleted = 0
            INNER JOIN tblContact D_C ON D.contact_id = D_C.contact_id AND D_C.is_deleted = 0
            LEFT OUTER JOIN tblSampleBatch SB ON SB.sample_id = S.sample_id AND SB.is_deleted = 0
            LEFT OUTER JOIN tblUser OWNER_USER ON A.sf_owner_user_id = OWNER_USER.sf_user_id
            -- LEFT OUTER JOIN tblUser RA_USER ON A.sf_ra_user_id = RA_USER.sf_user_id
            LEFT OUTER JOIN tblUser S_U ON E.created_by = S_U.user_id
        WHERE
            P.is_deleted = 0
            AND
            P_C.contact_type_id = 5");

    IF (s_pat_lname IS NOT NULL AND LENGTH(s_pat_lname) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(P_C.last_name) LIKE '", LOWER(s_pat_lname), "%'");
    END IF;

    IF (s_pat_fname IS NOT NULL AND LENGTH(s_pat_fname) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(P_C.first_name) LIKE '", LOWER(s_pat_fname), "%'");
    END IF;

    IF (s_protocol IS NOT NULL AND LENGTH(s_protocol) > 0) THEN
        SET @query_sql := CONCAT(@query_sql,"
            AND
            LOWER(TEST.protocol) LIKE '", LOWER(s_protocol), "%'");
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
    END IF;

    
    IF (s_ext_sample_id IS NOT NULL AND LENGTH(s_ext_sample_id) > 0) THEN
    SET @query_sql := CONCAT(@query_sql,"
        AND
        LOWER(S.external_sample_id) LIKE '", LOWER(s_ext_sample_id), "%'");
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
        LOWER(S.clinic_assistant_name) LIKE '%", LOWER(s_ra_last_name), "%'
        -- (
        --     LOWER(RA_USER.last_name) LIKE '", LOWER(s_ra_last_name), "%'
        --     OR
        --     LOWER(A.clinical_associate) LIKE '% ", LOWER(s_ra_last_name), "%'
        -- )
        ");
    END IF;

    
    IF (s_is_preintake_needed IS NOT NULL) THEN
    SET @query_sql := CONCAT(@query_sql,"
        AND
        S.is_preintake_needed = ", s_is_preintake_needed, " AND A.account_id NOT IN (17,364)"); -- Exclude Proove accounts
    END IF;

    SET @query_sql := CONCAT(@query_sql,"
        ORDER BY ", sort_field," ", sort_dir,"
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
