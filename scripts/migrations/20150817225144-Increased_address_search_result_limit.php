<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150817225144 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Clinic_Search`;

CREATE PROCEDURE `pr_Clinic_Search`(
	IN new_session VARCHAR(255),
	IN s_account_id  INT UNSIGNED,
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
			ELSE "C.company_name"
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
			WHEN page_amount IS NULL THEN 100
			ELSE page_amount
		END
	INTO
		page_amount;

	
	SET @query_sql := CONCAT("
		SELECT SQL_CALC_FOUND_ROWS
			 T.clinic_id
			,T.sf_location_id
			,T.contact_id
			,T.external_site_id
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
			,AD.address_id
			,AD.contact_id
			,AD.address_name
			,AD.street
			,AD.street2
			,AD.city
			,AD.state
			,AD.zip
			,AD.country
		FROM
			tblAccount A
			INNER JOIN tblAccountContact AC ON A.account_id = AC.account_id
			INNER JOIN tblContact C ON AC.contact_id = C.contact_id
			INNER JOIN tblClinic T ON T.contact_id = C.contact_id
			INNER JOIN tblAddress AD ON AD.contact_id = C.contact_id
		WHERE
			A.account_id = ", s_account_id, "
			AND
			A.is_deleted = 0
			AND
			T.is_deleted = 0
			AND
			AC.is_deleted = 0
			AND
			C.is_deleted = 0
			AND
			AD.is_deleted = 0");

	

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
DROP PROCEDURE IF EXISTS `pr_Clinic_Search`;

CREATE PROCEDURE `pr_Clinic_Search`(
	IN new_session VARCHAR(255),
	IN s_account_id  INT UNSIGNED,
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
			ELSE "C.company_name"
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
		SELECT SQL_CALC_FOUND_ROWS
			 T.clinic_id
			,T.sf_location_id
			,T.contact_id
			,T.external_site_id
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
			,AD.address_id
			,AD.contact_id
			,AD.address_name
			,AD.street
			,AD.street2
			,AD.city
			,AD.state
			,AD.zip
			,AD.country
		FROM
			tblAccount A
			INNER JOIN tblAccountContact AC ON A.account_id = AC.account_id
			INNER JOIN tblContact C ON AC.contact_id = C.contact_id
			INNER JOIN tblClinic T ON T.contact_id = C.contact_id
			INNER JOIN tblAddress AD ON AD.contact_id = C.contact_id
		WHERE
			A.account_id = ", s_account_id, "
			AND
			A.is_deleted = 0
			AND
			T.is_deleted = 0
			AND
			AC.is_deleted = 0
			AND
			C.is_deleted = 0
			AND
			AD.is_deleted = 0");

	

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
