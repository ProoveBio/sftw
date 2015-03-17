<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150317233801 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
-- ------------------------------------------
-- Procedure Updates
-- ------------------------------------------

DROP PROCEDURE IF EXISTS pr_Document_Upsert;

DELIMITER ;;
CREATE PROCEDURE `pr_Document_Upsert`(
	IN new_session VARCHAR(255),
	INOUT new_document_id INT UNSIGNED,
	IN new_encounter_id INT UNSIGNED,
	IN new_external_sample_id VARCHAR(45),
	IN new_is_temp TINYINT UNSIGNED,
	IN new_is_deleted TINYINT UNSIGNED,
	IN new_label VARCHAR(255),
	IN new_document_type_id SMALLINT UNSIGNED
)
this:BEGIN
	DECLARE new_update_comment TEXT;
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
	
	IF (EXISTS(SELECT 1 FROM tblDocument WHERE document_id = new_document_id)) THEN
	BEGIN -- UPDATE
		UPDATE 
			`tblDocument`
		SET 
			encounter_id:=new_encounter_id,
			external_sample_id:=new_external_sample_id,
			is_temp:=new_is_temp,
			is_deleted:=new_is_deleted,
			label:=new_label,
			document_type_id:=new_document_type_id
		WHERE
			document_id = new_document_id;

	END;

--  Normally, this is where we would handle inserts, but we don't want to insert any documents here
--	ELSEIF (new_document_id IS NULL OR new_document_id = 0) THEN
--

	ELSE
	BEGIN -- NONEXISTENT ID PASSED IN FOR UPDATE
		SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Document ID given does not match any existing Document, cannot update.';
	END;
	END IF;
END;;
DELIMITER ;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS pr_Document_Upsert;
EOT;
		$this->querySQL($sql);
	}

}
