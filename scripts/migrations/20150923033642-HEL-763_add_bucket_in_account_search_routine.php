<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150923033642 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Account_GetSingle`;

CREATE PROCEDURE `pr_Account_GetSingle`(
	IN new_session VARCHAR(255),
	IN s_account_id  INT UNSIGNED
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
		T.account_id
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
,T.protocol
,T.bucket
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
		tblAccount T
		INNER JOIN tblContact C ON T.contact_id = C.contact_id
	WHERE
		account_id = s_account_id
		AND
		T.is_deleted = 0
		AND
		C.is_deleted = 0;
END
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS `pr_Account_GetSingle`;

CREATE PROCEDURE `pr_Account_GetSingle`(
	IN new_session VARCHAR(255),
	IN s_account_id  INT UNSIGNED
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
		T.account_id
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
,T.protocol
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
		tblAccount T
		INNER JOIN tblContact C ON T.contact_id = C.contact_id
	WHERE
		account_id = s_account_id
		AND
		T.is_deleted = 0
		AND
		C.is_deleted = 0;
END
EOT;
		$this->querySQL($sql);
	}

}
