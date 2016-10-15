<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20161015205733 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Account_Search`;

CREATE PROCEDURE `pr_Account_Search`(
	IN new_session VARCHAR(255),
	IN s_account_name  VARCHAR(255),
	IN s_doc_lname VARCHAR(255),
	IN s_npi VARCHAR(10),
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
			WHEN sort_field IS NULL THEN "C.company_name"
			WHEN sort_field = "npi" THEN "OTHER_D.npi, T.group_npi"
			WHEN sort_field = "last_name" THEN "OTHER_C.last_name"
			WHEN sort_field = "account_company_name" THEN "C.company_name"
			ELSE "C.company_name"
		END
	INTO
		sort_field;

	SELECT
		CASE
			WHEN UPPER(sort_dir) = "DESC" THEN "DESC"
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
			-- ACCOUNT FIELDS
			T.account_id
				,T.parent_id
				,T.contact_id
				,T.sf_account_id
				,T.status
				,T.tax_id_num
				,T.type
				,T.description
				,T.clinical_associate
				,T.account_num
				,T.agreement_date
				,T.group_npi
				,T.bucket
				,C.contact_type_id AS account_contact_type_id
				,C.is_company AS account_is_company
				,C.company_name AS account_company_name
				,C.title AS account_title
				,C.prefix AS account_prefix
				,C.first_name AS account_first_name
				,C.middle_name AS account_middle_name
				,C.last_name AS account_last_name
				,C.suffix AS account_suffix
				,C.email AS account_email
			-- DOCTOR FIELDS
				,OTHER_D.doctor_id
				,OTHER_D.contact_id
				,OTHER_D.npi
				,OTHER_D.license
				,OTHER_D.medicare
				,OTHER_D.medicaid
				,OTHER_D.tricare
				,OTHER_C.contact_type_id AS doctor_contact_type_id
				,OTHER_C.is_company AS doctor_is_company
				,OTHER_C.company_name AS doctor_company_name
				,OTHER_C.title AS doctor_title
				,OTHER_C.prefix AS doctor_prefix
				,OTHER_C.first_name AS doctor_first_name
				,OTHER_C.middle_name AS doctor_middle_name
				,OTHER_C.last_name AS doctor_last_name
				,OTHER_C.suffix AS doctor_suffix
				,OTHER_C.email AS doctor_email
				,ADDR.street
				,ADDR.street2
				,ADDR.city
				,ADDR.state
				,ADDR.zip
		FROM
			tblAccount T
			INNER JOIN tblContact C ON T.contact_id = C.contact_id
			INNER JOIN tblAccountContact OTHER_AC ON OTHER_AC.account_id = T.account_id
			INNER JOIN tblContact OTHER_C ON OTHER_AC.contact_id = OTHER_C.contact_id AND OTHER_C.contact_type_id = 3
			INNER JOIN tblDoctor OTHER_D ON OTHER_D.contact_id = OTHER_C.contact_id

			LEFT JOIN tblAddress ADDR ON ADDR.contact_id = C.contact_id AND ADDR.address_name = 'Shipping'
		WHERE
			T.status NOT IN ('Terminated', 'Pending')
            AND
            T.parent_id IS NOT NULL
            AND
            OTHER_D.is_deleted = 0 AND OTHER_C.is_deleted = 0
			AND
			C.contact_type_id = 1"
	);

	
	IF (s_account_name IS NOT NULL AND LENGTH(s_account_name) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			LOWER(C.company_name) LIKE '", LOWER(s_account_name), "%'");
	END IF;

	
	IF (s_doc_lname IS NOT NULL AND LENGTH(s_doc_lname) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			LOWER(OTHER_C.last_name) LIKE '", LOWER(s_doc_lname), "%' AND OTHER_C.contact_type_id = 3");
	END IF;

	
	IF (s_npi IS NOT NULL AND LENGTH(s_npi) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			(T.group_npi LIKE '", s_npi, "%' OR OTHER_D.npi LIKE '", s_npi, "%')");
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
DROP PROCEDURE IF EXISTS `pr_Account_Search`;

CREATE DEFINER=`root`@`localhost` PROCEDURE `pr_Account_Search`(
	IN new_session VARCHAR(255),
	IN s_account_name  VARCHAR(255),
	IN s_doc_lname VARCHAR(255),
	IN s_npi VARCHAR(10),
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
			WHEN sort_field IS NULL THEN "C.company_name"
			WHEN sort_field = "npi" THEN "OTHER_D.npi, T.group_npi"
			WHEN sort_field = "last_name" THEN "OTHER_C.last_name"
			WHEN sort_field = "account_company_name" THEN "C.company_name"
			ELSE "C.company_name"
		END
	INTO
		sort_field;

	SELECT
		CASE
			WHEN UPPER(sort_dir) = "DESC" THEN "DESC"
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
			-- ACCOUNT FIELDS
			T.account_id
				,T.parent_id
				,T.contact_id
				,T.sf_account_id
				,T.status
				,T.tax_id_num
				,T.type
				,T.description
				,T.clinical_associate
				,T.account_num
				,T.agreement_date
				,T.group_npi
				,T.bucket
				,C.contact_type_id AS account_contact_type_id
				,C.is_company AS account_is_company
				,C.company_name AS account_company_name
				,C.title AS account_title
				,C.prefix AS account_prefix
				,C.first_name AS account_first_name
				,C.middle_name AS account_middle_name
				,C.last_name AS account_last_name
				,C.suffix AS account_suffix
				,C.email AS account_email
			-- DOCTOR FIELDS
				,OTHER_D.doctor_id
				,OTHER_D.contact_id
				,OTHER_D.npi
				,OTHER_D.license
				,OTHER_D.medicare
				,OTHER_D.medicaid
				,OTHER_D.tricare
				,OTHER_C.contact_type_id AS doctor_contact_type_id
				,OTHER_C.is_company AS doctor_is_company
				,OTHER_C.company_name AS doctor_company_name
				,OTHER_C.title AS doctor_title
				,OTHER_C.prefix AS doctor_prefix
				,OTHER_C.first_name AS doctor_first_name
				,OTHER_C.middle_name AS doctor_middle_name
				,OTHER_C.last_name AS doctor_last_name
				,OTHER_C.suffix AS doctor_suffix
				,OTHER_C.email AS doctor_email
				,ADDR.street
				,ADDR.street2
				,ADDR.city
				,ADDR.state
				,ADDR.zip
		FROM
			tblAccount T
			INNER JOIN tblContact C ON T.contact_id = C.contact_id
			INNER JOIN tblAddress ADDR ON ADDR.contact_id = C.contact_id
			INNER JOIN tblAccountContact OTHER_AC ON OTHER_AC.account_id = T.account_id
			INNER JOIN tblContact OTHER_C ON OTHER_AC.contact_id = OTHER_C.contact_id AND OTHER_C.contact_type_id = 3
			INNER JOIN tblDoctor OTHER_D ON OTHER_D.contact_id = OTHER_C.contact_id
		WHERE
			T.status NOT IN ('Terminated', 'Pending')
            AND
            T.parent_id IS NOT NULL
            AND
            OTHER_D.is_deleted = 0 AND OTHER_C.is_deleted = 0
			AND
			C.contact_type_id = 1"
	);

	
	IF (s_account_name IS NOT NULL AND LENGTH(s_account_name) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			LOWER(C.company_name) LIKE '", LOWER(s_account_name), "%'");
	END IF;

	
	IF (s_doc_lname IS NOT NULL AND LENGTH(s_doc_lname) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			LOWER(OTHER_C.last_name) LIKE '", LOWER(s_doc_lname), "%' AND OTHER_C.contact_type_id = 3");
	END IF;

	
	IF (s_npi IS NOT NULL AND LENGTH(s_npi) > 0) THEN
		SET @query_sql := CONCAT(@query_sql,"
			AND
			(T.group_npi LIKE '", s_npi, "%' OR OTHER_D.npi LIKE '", s_npi, "%')");
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
