<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150325211056 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS pr_Admin_GetIntakeHistory;

CREATE PROCEDURE `pr_Admin_GetIntakeHistory`(
        IN new_session VARCHAR(255),
        IN s_date_start DATETIME,
        IN s_date_end DATETIME
)
this:BEGIN
	DECLARE date_start DATETIME DEFAULT CAST(CONVERT_TZ(s_date_start,'+0:00','+8:00') AS DATETIME);
        DECLARE date_end DATETIME DEFAULT CAST(CONVERT_TZ(s_date_end,'+0:00','+8:00') AS DATETIME);

        SELECT * FROM
        (
	SELECT
              	A_C.company_name AS "Account_Name"
                ,DATE(E.encounter_datetime) AS "Date_of_Collection"
                ,DATE(S.received_datetime) AS "Date_Received"
                ,CAST(CONVERT_TZ(E.created_datetime,'+0:00','-8:00') AS DATE) AS "Date_Entered_Into_Helix"
                ,GROUP_CONCAT(T.test_type_id ORDER BY T.test_type_id SEPARATOR ', ') AS tests
                ,GROUP_CONCAT(T.test_status_id ORDER BY T.test_type_id SEPARATOR ', ') AS test_status
        FROM
            	tblAccount A
                INNER JOIN tblContact A_C ON A.contact_id = A_C.contact_id
                INNER JOIN tblEncounter E ON E.account_id = A.account_id AND E.is_deleted = 0
                INNER JOIN tblSample S ON E.encounter_id = S.encounter_id AND S.is_deleted = 0
                INNER JOIN tblTest T ON T.encounter_id = E.encounter_id AND T.is_deleted = 0
        WHERE
             	E.created_datetime BETWEEN date_start AND date_end AND E.is_deleted = 0
        GROUP BY
                E.encounter_id
        ) OUT_T
        ORDER BY OUT_T.Date_of_Collection, OUT_T.Account_Name;

END;;
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS pr_Admin_GetIntakeHistory;

CREATE DEFINER=`root`@`%` PROCEDURE `pr_Admin_GetIntakeHistory`(
	IN new_session VARCHAR(255),
	IN s_date_start DATETIME,
	IN s_date_end DATETIME
)
this:BEGIN
	DECLARE date_start DATETIME DEFAULT CAST(CONVERT_TZ(s_date_start,'+0:00','+8:00') AS DATETIME);
	DECLARE date_end DATETIME DEFAULT CAST(CONVERT_TZ(s_date_end,'+0:00','+8:00') AS DATETIME);

	SELECT * FROM
	(
	SELECT
		A_C.company_name AS "Account_Name"
		,DATE(E.encounter_datetime) AS "Date_of_Collection"
		,DATE(S.received_datetime) AS "Date_Received"
		,CAST(CONVERT_TZ(E.created_datetime,'+0:00','-8:00') AS DATE) AS "Date_Entered_Into_Helix"
		,SUM(
			CASE WHEN T_PBIO1.test_id IS NOT NULL
			THEN 1 ELSE 0
			END
		) AS "PBIO1"
		,SUM(
			CASE WHEN T_PBIO2.test_id IS NOT NULL
			THEN 1 ELSE 0
			END
		) AS "PBIO2"
		,SUM(
			CASE WHEN T_PBIO3.test_id IS NOT NULL
			THEN 1 ELSE 0
			END
		) AS "PBIO3"
		,SUM(
			CASE WHEN T_PBIO4.test_id IS NOT NULL
			THEN 1 ELSE 0
			END
		) AS "PBIO4"
	FROM
		tblAccount A
		INNER JOIN tblContact A_C ON A.contact_id = A_C.contact_id
		INNER JOIN tblEncounter E ON E.account_id = A.account_id AND E.is_deleted = 0
		INNER JOIN tblSample S ON E.encounter_id = S.encounter_id AND S.is_deleted = 0
		LEFT OUTER JOIN tblTest T_PBIO1 ON T_PBIO1.encounter_id = E.encounter_id AND T_PBIO1.test_type_id = 3 AND T_PBIO1.is_deleted = 0
		LEFT OUTER JOIN tblTest T_PBIO2 ON T_PBIO2.encounter_id = E.encounter_id AND T_PBIO2.test_type_id = 5 AND T_PBIO2.is_deleted = 0
		LEFT OUTER JOIN tblTest T_PBIO3 ON T_PBIO3.encounter_id = E.encounter_id AND T_PBIO3.test_type_id = 6 AND T_PBIO3.is_deleted = 0
		LEFT OUTER JOIN tblTest T_PBIO4 ON T_PBIO4.encounter_id = E.encounter_id AND T_PBIO4.test_type_id = 7 AND T_PBIO4.is_deleted = 0
	WHERE
		E.created_datetime BETWEEN date_start AND date_end AND E.is_deleted = 0
	GROUP BY
		E.encounter_id
	) OUT_T
	ORDER BY OUT_T.Date_of_Collection, OUT_T.Account_Name;
	
END;;
EOT;
		$this->querySQL($sql);
	}

}
