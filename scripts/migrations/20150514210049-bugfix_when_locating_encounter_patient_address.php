<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150514210049 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS pr_Billing_GetPatientDownload;

CREATE PROCEDURE `pr_Billing_GetPatientDownload`(
	IN new_session VARCHAR(255),
	IN new_billing_download_id  INT UNSIGNED,
	IN new_payer_type VARCHAR(255),
	IN new_start_date DATETIME,
	IN new_end_date DATETIME
)
this:BEGIN
	DECLARE today_cutoff DATE DEFAULT CAST(CONVERT_TZ(NOW(),'+0:00','-8:00') AS DATE);
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

	
	IF (new_start_date IS NULL OR new_end_date IS NULL) THEN
	BEGIN
		SET new_start_date := (SELECT last_download_datetime FROM tblBillingDownloadPatient ORDER BY last_download_datetime DESC LIMIT 1);

		IF (EXISTS (SELECT 1 FROM tblDatabaseConfiguration WHERE database_configuration_id = 2 and configuration_value = 1)) THEN
			SET new_end_date := NOW();
		ELSE
			SET new_end_date := today_cutoff;
		END IF;

		TRUNCATE TABLE tblBillingDownloadPatient;
		INSERT INTO tblBillingDownloadPatient(last_download_datetime) VALUE (new_end_date);
	END;
	END IF;

	
	IF (new_billing_download_id IS NULL) THEN
	BEGIN

	SELECT
		 "TOR" AS "Practice Code" 
		,COALESCE(P.external_patient_id, SAMPLE.external_sample_id) AS "Patient Account Number" 
		,CONCAT(P_C.last_name, ", ", P_C.first_name) AS "Patient Name" 
		,P_C_A.street AS "Patient Address 1" 
		,P_C_A.street2 AS "Patient Address 2"
		,CONCAT(P_C_A.city, ", ", P_C_A.state) AS "Patient City, State" 
		,P_C_A.zip AS "Patient Zip Code" 
		,CONCAT(P_C_PH.area_code, P_C_PH.local_number) AS "Patient Home Phone"
		,P_C_PH.extension AS "Patient Home Phone Ext"
		,CONCAT(P_C_PW.area_code, P_C_PW.local_number) AS "Patient Work Phone"
		,P_C_PW.extension AS "Patient Work Phone Ext"
		,CONCAT(P_C_PO.area_code, P_C_PO.local_number) AS "Patient Other Phone"
		,P_C_PO.extension AS "Patient Other Phone Ext"
		,P.birth_date AS "Patient DOB" 
		,NULL AS "Patient Work/School Status"
		,P.social_security_num AS "Patient SSN"
		,P_G.gender_name AS "Patient Gender" 
		,NULL AS "Patient Marital Status"
		,PAY_WC_C.company_name AS "Patient Employer Name"
		,CONCAT(PAY_WC_C_A.street, PAY_WC_C_A.street2) AS "Patient Employer Address"
		,CONCAT(PAY_WC_C_A.city, ", ", PAY_WC_C_A.state)  AS "Patient Employer City, State"
		,PAY_WC_C_A.zip AS "Patient Employer Zip Code"
		,P_C.email AS "Patient Email Address"
		,NULL AS "Medical Record Number"
		,NULL AS "Drivers License Number"
		,"PROOVE" AS "Provider" 
		
		,CASE
			WHEN ENCOUNTER.clinic_id IS NOT NULL THEN CONCAT(ACC_C.company_name,"; Location: ",CLINIC_C.company_name)
			ELSE ACC_C.company_name
		END AS "Location" 
		
		,CONCAT(DOCTOR_C.last_name, ", ", DOCTOR_C.first_name) AS "Referring Physician" 
		,CASE
		 	WHEN PAYCO_P.payer_type_id = 6 THEN "WC" 
		 	WHEN PAYCO_P.payer_type_id = 4 THEN "MCD" 
		 	WHEN PAYCO_P.payer_type_id = 2 THEN "MC" 
		 	WHEN PAYCO_P.payer_type_id = 8 THEN "MVA" 
		 	WHEN LOWER(PAYCO_P_C.company_name) RLIKE "((bcbs)|(blue(.)?cross(.)?blue(.)?shield))" THEN "BCBS" 
		 	WHEN PAYCO_P.payer_type_id = 5 THEN "CI"  
		 	WHEN PAYCO_P.payer_type_id = 9 THEN "TRICARE"  
		 	ELSE "UNKNOWN"
		 END AS "Financial Class"
		,NULL AS "Guarantor Name"
		,NULL AS "Guarantor Address 1"
		,NULL AS "Guarantor Address 2"
		,NULL AS "Guarantor City, State"
		,NULL AS "Guarantor Zip Code"
		,NULL AS "Guarantor Home Phone"
		,NULL AS "Guarantor Home Phone Ext"
		,NULL AS "Guarantor Work Phone"
		,NULL AS "Guarantor Work Phone Ext"
		,NULL AS "Guarantor Other Phone"
		,NULL AS "Guarantor Other Phone Ext"
		,NULL AS "Guarantor DOB"
		,NULL AS "Guarantor Gender"
		,NULL AS "Guarantor Employer Name"
		,NULL AS "Guarantor Employer Address"
		,NULL AS "Guarantor Employer City, State"
		,NULL AS "Guarantor Employer Zip"
		,NULL AS "Guarantor Email Address"
		,NULL AS "Guarantor SSN"
		,NULL AS "Guarantor Marital Status"
		,NULL AS "Emergency Contact"
		,NULL AS "Primary Care Physician"
		,NULL AS "Family Physician"
		,NULL AS "First Symptom Date"
		,NULL AS "First Treated Date"
		,NULL AS "Last Treated Date by Referrer"
		,NULL AS "Last Admission Date"
		,NULL AS "Last Discharge Date"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL WHEN PAYCO_P.payer_type_id = 6 THEN "W" ELSE "P" END AS "Primary Insurance Status (P/S/T/I/O/A/WH)" 
		,NULL AS "Primary Insurance Company Code" 
		,PAYCO_P_C.company_name AS "Primary Insurance Company Name" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE
			CASE WHEN PAYCO_P.payer_type_id = 6 THEN P.social_security_num ELSE PAY_P.policy_num END 
		END AS "Primary Insurance Policy Number" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE PAY_P.group_num END AS "Primary Insurance Group Number"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE "Y" END AS "Primary Insurance Assignment" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE CONCAT(PAY_P_SUB_C.last_name, ", ", PAY_P_SUB_C.first_name) END AS "Primary Insured Name"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE PAY_P.subscriber_relationship_type_id END AS "Primary Patient Relation to Insured" 
		,NULL AS "Primary Insured Gender"
		,NULL AS "Primary Insured Birth"
		,NULL AS "Primary Insured Address"
		,NULL AS "Primary Insured City, State"
		,NULL AS "Primary Insured Zip"
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_WC_C.company_name ELSE NULL
		END AS "Primary Insured Employer" 
		,NULL AS "Primary Insured Telephone Home"
		,NULL AS "Primary Insured Telephone Work"
		,NULL AS "Primary Insurance Plan Name"
		,NULL AS "Primary Copay Amount"
		,NULL AS "Primary Effective Date Start"
		,NULL AS "Primary Effective Date End"
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN "WC" ELSE NULL
		END AS "Primary Accident Type WAOP" 
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_P.workers_comp_injury_date ELSE NULL
		END AS "Primary Accident Date" 
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_WC_C_A.state ELSE NULL 
		END AS "Primary Accident State" 
		,NULL AS "Primary Insurance Authorization Number for Treatment"
		,NULL AS "Primary PPO / HMO"
		,NULL AS "Primary Family Planning Indicator"
		,NULL AS "Primary EPSDT / PGH Indicator"
		,NULL AS "Primary Insurance Claim Note"
		,NULL AS "Primary Insurance Claim Note"
		,NULL AS "Primary MSP Type"
		,NULL AS "Primary Emergency Treatment"
		,NULL AS "Primary Insurance Internal  Use/Note"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE "S" END AS "Secondary Insurance Status (P/S/T/I/O/A/WH)"
		,NULL AS "Secondary Insurance Company Code" 
		,PAYCO_S_C.company_name AS "Secondary Insurance Company Name"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.policy_num END AS "Secondary Insurance Policy Number"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.group_num END AS "Secondary Insurance Group Number"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE "Y" END AS "Secondary Insurance Assignment"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE CONCAT(PAY_S_SUB_C.last_name, ", ", PAY_S_SUB_C.first_name) END AS "Secondary Insured Name"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.subscriber_relationship_type_id END AS "Secondary Patient Relation to Insured"
		,NULL AS "Secondary Insured Gender"
		,NULL AS "Secondary Insured Birth"
		,NULL AS "Secondary Insured Address"
		,NULL AS "Secondary Insured City, State"
		,NULL AS "Secondary Insured Zip"
		,NULL AS "Secondary Insured Employer"
		,NULL AS "Secondary Insured Telephone Home"
		,NULL AS "Secondary Insured Telephone Work"
		,NULL AS "Secondary Insurance Plan Name"
		,NULL AS "Secondary Copay Amount"
		,NULL AS "Secondary Effective Date Start"
		,NULL AS "Secondary Effective Date End"
		,NULL AS "Secondary Accident Type WAOP"
		,NULL AS "Secondary Accident Date"
		,NULL AS "Secondary Accident State"
		,NULL AS "Secondary Insurance Authorization Number for Treatment"
		,NULL AS "Secondary PPO / HMO"
		,NULL AS "Secondary Family Planning Indicator"
		,NULL AS "Secondary EPSDT / PGH Indicator"
		,NULL AS "Secondary Insurance Claim Note"
		,NULL AS "Secondary Insurance Claim Note"
		,NULL AS "Secondary MSP Type"
		,NULL AS "Secondary Emergency Treatment"
		,NULL AS "Secondary Insurance Internal  Use/Note"
		,NULL AS "Tertiary Insurance Status (P/S/T/I/O/A/WH)"
		,NULL AS "Tertiary Insurance Company Code"
		,NULL AS "Tertiary Insurance Company Name"
		,NULL AS "Tertiary Insurance Policy Number"
		,NULL AS "Tertiary Insurance Group Number"
		,NULL AS "Tertiary Insurance Assignment"
		,NULL AS "Tertiary Insured Name"
		,NULL AS "Tertiary Patient Relation to Insured"
		,NULL AS "Tertiary Insured Gender"
		,NULL AS "Tertiary Insured Birth"
		,NULL AS "Tertiary Insured Address"
		,NULL AS "Tertiary Insured City, State"
		,NULL AS "Tertiary Insured Zip"
		,NULL AS "Tertiary Insured Employer"
		,NULL AS "Tertiary Insured Telephone Home"
		,NULL AS "Tertiary Insured Telephone Work"
		,NULL AS "Tertiary Insurance Plan Name"
		,NULL AS "Tertiary Copay Amount"
		,NULL AS "Tertiary Effective Date Start"
		,NULL AS "Tertiary Effective Date End"
		,NULL AS "Tertiary Accident Type WAOP"
		,NULL AS "Tertiary Accident Date"
		,NULL AS "Tertiary Accident State"
		,NULL AS "Tertiary Insurance Authorization Number for Treatment"
		,NULL AS "Tertiary PPO / HMO"
		,NULL AS "Tertiary Family Planning Indicator"
		,NULL AS "Tertiary EPSDT / PGH Indicator"
		,NULL AS "Tertiary Insurance Claim Note"
		,NULL AS "Tertiary Insurance Claim Note"
		,NULL AS "Tertiary MSP Type"
		,NULL AS "Tertiary Emergency Treatment"
		,NULL AS "Tertiary Insurance Internal  Use/Note"
		,NULL AS "Statement Hold"
		,NULL AS "hold reason"
		,NULL AS "Stop claims"
		,NULL AS "Reason"
		,NULL AS "Stop Charges"
		,NULL AS "Reason"
		,NULL AS "Last statement date"
		,NULL AS "Next statement remark code"
	FROM
		tblPatient P 
		INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0 
		INNER JOIN tblGender P_G ON P.gender_id = P_G.gender_id  
		INNER JOIN tblEncounter ENCOUNTER ON ENCOUNTER.patient_id = P.patient_id AND ENCOUNTER.is_deleted = 0
		INNER JOIN tblAccount ACCOUNT ON ACCOUNT.account_id = ENCOUNTER.account_id AND ACCOUNT.is_deleted = 0
		INNER JOIN tblContact ACC_C ON ACCOUNT.contact_id = ACC_C.contact_id AND ACC_C.is_deleted = 0 
		INNER JOIN (
			SELECT
				INNER_ENC_PAY.encounter_id
				,INNER_PAY_P.*
			FROM
				tblEncounterPayer INNER_ENC_PAY
				INNER JOIN tblPayer INNER_PAY_P ON INNER_ENC_PAY.payer_id = INNER_PAY_P.payer_id AND INNER_PAY_P.coverage_order = 1 AND INNER_PAY_P.is_deleted = 0 
			WHERE
				INNER_ENC_PAY.is_deleted = 0
		) AS PAY_P ON PAY_P.encounter_id = ENCOUNTER.encounter_id
		LEFT OUTER JOIN (
			SELECT
				INNER_ENC_PAY_S.encounter_id
				,INNER_PAY_S.*
			FROM
				tblEncounterPayer INNER_ENC_PAY_S
				INNER JOIN tblPayer INNER_PAY_S ON INNER_ENC_PAY_S.payer_id = INNER_PAY_S.payer_id AND INNER_PAY_S.coverage_order = 2 AND INNER_PAY_S.is_deleted = 0 
			WHERE
				INNER_ENC_PAY_S.is_deleted = 0
		) AS PAY_S ON PAY_S.encounter_id = ENCOUNTER.encounter_id
		LEFT OUTER JOIN tblSample SAMPLE ON ENCOUNTER.encounter_id = SAMPLE.encounter_id AND SAMPLE.is_deleted = 0
		LEFT OUTER JOIN tblContact PAY_P_SUB_C ON PAY_P_SUB_C.contact_id = PAY_P.subscriber_contact_id AND PAY_P_SUB_C.is_deleted = 0 
		LEFT OUTER JOIN tblAddress P_C_A ON ENCOUNTER.patient_address_id = P_C_A.address_id AND P_C_A.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PH ON P.contact_id = P_C_PH.contact_id AND P_C_PH.phone_name = "Home" AND P_C_PH.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PW ON P.contact_id = P_C_PW.contact_id AND P_C_PW.phone_name = "Work" AND P_C_PW.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PO ON P.contact_id = P_C_PO.contact_id AND P_C_PO.phone_name = "Mobile" AND P_C_PO.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAY_S_SUB_C ON PAY_S_SUB_C.contact_id = PAY_S.subscriber_contact_id  AND PAY_S_SUB_C.is_deleted = 0 
		LEFT OUTER JOIN tblClinic CLINIC ON CLINIC.clinic_id = ENCOUNTER.clinic_id AND CLINIC.is_deleted = 0 
		LEFT OUTER JOIN tblContact CLINIC_C ON CLINIC_C.contact_id = CLINIC.contact_id  AND CLINIC_C.is_deleted = 0 
		LEFT OUTER JOIN tblDoctor DOCTOR ON DOCTOR.doctor_id = ENCOUNTER.doctor_id AND DOCTOR.is_deleted = 0 
		LEFT OUTER JOIN tblContact DOCTOR_C ON DOCTOR_C.contact_id = DOCTOR.contact_id AND DOCTOR_C.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAY_WC_C ON PAY_P.workers_comp_employer_contact_id = PAY_WC_C.contact_id AND PAY_WC_C.is_deleted = 0 
		LEFT OUTER JOIN tblAddress PAY_WC_C_A ON PAY_P.workers_comp_employer_contact_id = PAY_WC_C_A.contact_id AND PAY_WC_C_A.is_deleted = 0
		LEFT OUTER JOIN tblPayerCompany PAYCO_P ON PAYCO_P.payer_company_id = PAY_P.payer_company_id AND PAYCO_P.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAYCO_P_C ON PAYCO_P_C.contact_id = PAYCO_P.contact_id AND PAYCO_P_C.is_deleted = 0 
		LEFT OUTER JOIN tblPayerCompany PAYCO_S ON PAYCO_S.payer_company_id = PAY_S.payer_company_id AND PAYCO_S.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAYCO_S_C ON PAYCO_S_C.contact_id = PAYCO_S.contact_id AND PAYCO_S_C.is_deleted = 0 
		LEFT OUTER JOIN tblTest TEST ON TEST.encounter_id = ENCOUNTER.encounter_id AND TEST.is_deleted = 0 
	WHERE
		P.is_deleted = 0
		AND
		TEST.test_status_id > 3
		AND
		SAMPLE.is_preintake_needed = 0
		AND
		(
			(new_payer_type = "unknown_payer_type" AND PAYCO_P.payer_type_id = 1)
			OR
			(new_payer_type = "medicare_medicaid" AND PAYCO_P.payer_type_id IN (2, 4))
			OR
			(new_payer_type = "client_account_billing" AND PAYCO_P.payer_type_id = 3)
			OR
			(new_payer_type = "private_insurance" AND PAYCO_P.payer_type_id = 5)
			OR
			(new_payer_type = "workers_comp" AND PAYCO_P.payer_type_id = 6)
			OR
			(new_payer_type = "patient_cash" AND PAYCO_P.payer_type_id = 7)
			OR
			(new_payer_type = "patient_assistance_program" AND PAYCO_P.payer_type_id = 8)
			OR
			(new_payer_type = "tricare" AND PAYCO_P.payer_type_id = 9)
			OR
			new_payer_type IS NULL
		)
		AND
		(
			(
				(new_start_date IS NULL OR new_start_date <= P.created_datetime)
				AND
				(new_end_date IS NULL OR new_end_date >= P.created_datetime)
			)
			OR
			(
				(new_start_date IS NULL OR new_start_date <= P.modified_datetime)
				AND
				(new_end_date IS NULL OR new_end_date >= P.modified_datetime)
			)
			OR
			(
				(new_start_date IS NULL OR new_start_date <= SAMPLE.modified_datetime)
				AND
				(new_end_date IS NULL OR new_end_date >= SAMPLE.modified_datetime)
			)
			OR
			(
				(new_start_date IS NULL OR new_start_date <= TEST.modified_datetime)
				AND
				(new_end_date IS NULL OR new_end_date >= TEST.modified_datetime)
			)
		)
		AND
		(
			EXISTS (SELECT 1 FROM tblDatabaseConfiguration WHERE database_configuration_id = 2 and configuration_value = 1)
			OR
			(
				(P.modified_datetime IS NULL OR P.modified_datetime < today_cutoff)
				AND
				P.created_datetime < today_cutoff
			)
			OR
			(
				(SAMPLE.modified_datetime IS NULL OR SAMPLE.modified_datetime < today_cutoff)
				AND
				SAMPLE.created_datetime < today_cutoff
			)
			OR
			(
				(TEST.modified_datetime IS NULL OR TEST.modified_datetime < today_cutoff)
				AND
				TEST.created_datetime < today_cutoff
			)
		)
	GROUP BY P.patient_id 
	ORDER BY P.created_datetime DESC; 

	END;
	ELSE 
	BEGIN

	SELECT
		 "TOR" AS "Practice Code" 
		,COALESCE(P.external_patient_id, SAMPLE.external_sample_id) AS "Patient Account Number" 
		,CONCAT(P_C.last_name, ", ", P_C.first_name) AS "Patient Name" 
		,P_C_A.street AS "Patient Address 1" 
		,P_C_A.street2 AS "Patient Address 2"
		,CONCAT(P_C_A.city, ", ", P_C_A.state) AS "Patient City, State" 
		,P_C_A.zip AS "Patient Zip Code" 
		,CONCAT(P_C_PH.area_code, P_C_PH.local_number) AS "Patient Home Phone"
		,P_C_PH.extension AS "Patient Home Phone Ext"
		,CONCAT(P_C_PW.area_code, P_C_PW.local_number) AS "Patient Work Phone"
		,P_C_PW.extension AS "Patient Work Phone Ext"
		,CONCAT(P_C_PO.area_code, P_C_PO.local_number) AS "Patient Other Phone"
		,P_C_PO.extension AS "Patient Other Phone Ext"
		,P.birth_date AS "Patient DOB" 
		,NULL AS "Patient Work/School Status"
		,P.social_security_num AS "Patient SSN"
		,P_G.gender_name AS "Patient Gender" 
		,NULL AS "Patient Marital Status"
		,PAY_WC_C.company_name AS "Patient Employer Name"
		,CONCAT(PAY_WC_C_A.street, PAY_WC_C_A.street2) AS "Patient Employer Address"
		,CONCAT(PAY_WC_C_A.city, ", ", PAY_WC_C_A.state)  AS "Patient Employer City, State"
		,PAY_WC_C_A.zip AS "Patient Employer Zip Code"
		,P_C.email AS "Patient Email Address"
		,NULL AS "Medical Record Number"
		,NULL AS "Drivers License Number"
		,"PROOVE" AS "Provider" 
		
		,CASE
			WHEN ENCOUNTER.clinic_id IS NOT NULL THEN CONCAT(ACC_C.company_name,"; Location: ",CLINIC_C.company_name)
			ELSE ACC_C.company_name
		END AS "Location" 
		
		,CONCAT(DOCTOR_C.last_name, ", ", DOCTOR_C.first_name) AS "Referring Physician" 
		,CASE
		 	WHEN PAYCO_P.payer_type_id = 6 THEN "WC" 
		 	WHEN PAYCO_P.payer_type_id = 4 THEN "MCD" 
		 	WHEN PAYCO_P.payer_type_id = 2 THEN "MC" 
		 	WHEN PAYCO_P.payer_type_id = 8 THEN "MVA" 
		 	WHEN LOWER(PAYCO_P_C.company_name) RLIKE "((bcbs)|(blue(.)?cross(.)?blue(.)?shield))" THEN "BCBS" 
		 	WHEN PAYCO_P.payer_type_id = 5 THEN "CI"  
		 	WHEN PAYCO_P.payer_type_id = 9 THEN "TRICARE"  
		 	ELSE "UNKNOWN"
		 END AS "Financial Class"
		,NULL AS "Guarantor Name"
		,NULL AS "Guarantor Address 1"
		,NULL AS "Guarantor Address 2"
		,NULL AS "Guarantor City, State"
		,NULL AS "Guarantor Zip Code"
		,NULL AS "Guarantor Home Phone"
		,NULL AS "Guarantor Home Phone Ext"
		,NULL AS "Guarantor Work Phone"
		,NULL AS "Guarantor Work Phone Ext"
		,NULL AS "Guarantor Other Phone"
		,NULL AS "Guarantor Other Phone Ext"
		,NULL AS "Guarantor DOB"
		,NULL AS "Guarantor Gender"
		,NULL AS "Guarantor Employer Name"
		,NULL AS "Guarantor Employer Address"
		,NULL AS "Guarantor Employer City, State"
		,NULL AS "Guarantor Employer Zip"
		,NULL AS "Guarantor Email Address"
		,NULL AS "Guarantor SSN"
		,NULL AS "Guarantor Marital Status"
		,NULL AS "Emergency Contact"
		,NULL AS "Primary Care Physician"
		,NULL AS "Family Physician"
		,NULL AS "First Symptom Date"
		,NULL AS "First Treated Date"
		,NULL AS "Last Treated Date by Referrer"
		,NULL AS "Last Admission Date"
		,NULL AS "Last Discharge Date"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL WHEN PAYCO_P.payer_type_id = 6 THEN "W" ELSE "P" END AS "Primary Insurance Status (P/S/T/I/O/A/WH)" 
		,NULL 
		,PAYCO_P_C.company_name AS "Primary Insurance Company Name" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE
			CASE WHEN PAYCO_P.payer_type_id = 6 THEN P.social_security_num ELSE PAY_P.policy_num END 
		END AS "Primary Insurance Policy Number" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE PAY_P.group_num END AS "Primary Insurance Group Number"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE "Y" END AS "Primary Insurance Assignment" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE CONCAT(PAY_P_SUB_C.last_name, ", ", PAY_P_SUB_C.first_name) END AS "Primary Insured Name"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE PAY_P.subscriber_relationship_type_id END AS "Primary Patient Relation to Insured" 
		,NULL AS "Primary Insured Gender"
		,NULL AS "Primary Insured Birth"
		,NULL AS "Primary Insured Address"
		,NULL AS "Primary Insured City, State"
		,NULL AS "Primary Insured Zip"
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_WC_C.company_name ELSE NULL
		END AS "Primary Insured Employer" 
		,NULL AS "Primary Insured Telephone Home"
		,NULL AS "Primary Insured Telephone Work"
		,NULL AS "Primary Insurance Plan Name"
		,NULL AS "Primary Copay Amount"
		,NULL AS "Primary Effective Date Start"
		,NULL AS "Primary Effective Date End"
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN "WC" ELSE NULL
		END AS "Primary Accident Type WAOP" 
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_P.workers_comp_injury_date ELSE NULL
		END AS "Primary Accident Date" 
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_WC_C_A.state ELSE NULL 
		END AS "Primary Accident State" 
		,NULL AS "Primary Insurance Authorization Number for Treatment"
		,NULL AS "Primary PPO / HMO"
		,NULL AS "Primary Family Planning Indicator"
		,NULL AS "Primary EPSDT / PGH Indicator"
		,NULL AS "Primary Insurance Claim Note"
		,NULL AS "Primary Insurance Claim Note"
		,NULL AS "Primary MSP Type"
		,NULL AS "Primary Emergency Treatment"
		,NULL AS "Primary Insurance Internal  Use/Note"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE "S" END AS "Secondary Insurance Status (P/S/T/I/O/A/WH)"
		,NULL 
		,PAYCO_S_C.company_name AS "Secondary Insurance Company Name"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.policy_num END AS "Secondary Insurance Policy Number"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.group_num END AS "Secondary Insurance Group Number"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE "Y" END AS "Secondary Insurance Assignment"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE CONCAT(PAY_S_SUB_C.last_name, ", ", PAY_S_SUB_C.first_name) END AS "Secondary Insured Name"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.subscriber_relationship_type_id END AS "Secondary Patient Relation to Insured"
		,NULL AS "Secondary Insured Gender"
		,NULL AS "Secondary Insured Birth"
		,NULL AS "Secondary Insured Address"
		,NULL AS "Secondary Insured City, State"
		,NULL AS "Secondary Insured Zip"
		,NULL AS "Secondary Insured Employer"
		,NULL AS "Secondary Insured Telephone Home"
		,NULL AS "Secondary Insured Telephone Work"
		,NULL AS "Secondary Insurance Plan Name"
		,NULL AS "Secondary Copay Amount"
		,NULL AS "Secondary Effective Date Start"
		,NULL AS "Secondary Effective Date End"
		,NULL AS "Secondary Accident Type WAOP"
		,NULL AS "Secondary Accident Date"
		,NULL AS "Secondary Accident State"
		,NULL AS "Secondary Insurance Authorization Number for Treatment"
		,NULL AS "Secondary PPO / HMO"
		,NULL AS "Secondary Family Planning Indicator"
		,NULL AS "Secondary EPSDT / PGH Indicator"
		,NULL AS "Secondary Insurance Claim Note"
		,NULL AS "Secondary Insurance Claim Note"
		,NULL AS "Secondary MSP Type"
		,NULL AS "Secondary Emergency Treatment"
		,NULL AS "Secondary Insurance Internal  Use/Note"
		,NULL AS "Tertiary Insurance Status (P/S/T/I/O/A/WH)"
		,NULL AS "Tertiary Insurance Company Code"
		,NULL AS "Tertiary Insurance Company Name"
		,NULL AS "Tertiary Insurance Policy Number"
		,NULL AS "Tertiary Insurance Group Number"
		,NULL AS "Tertiary Insurance Assignment"
		,NULL AS "Tertiary Insured Name"
		,NULL AS "Tertiary Patient Relation to Insured"
		,NULL AS "Tertiary Insured Gender"
		,NULL AS "Tertiary Insured Birth"
		,NULL AS "Tertiary Insured Address"
		,NULL AS "Tertiary Insured City, State"
		,NULL AS "Tertiary Insured Zip"
		,NULL AS "Tertiary Insured Employer"
		,NULL AS "Tertiary Insured Telephone Home"
		,NULL AS "Tertiary Insured Telephone Work"
		,NULL AS "Tertiary Insurance Plan Name"
		,NULL AS "Tertiary Copay Amount"
		,NULL AS "Tertiary Effective Date Start"
		,NULL AS "Tertiary Effective Date End"
		,NULL AS "Tertiary Accident Type WAOP"
		,NULL AS "Tertiary Accident Date"
		,NULL AS "Tertiary Accident State"
		,NULL AS "Tertiary Insurance Authorization Number for Treatment"
		,NULL AS "Tertiary PPO / HMO"
		,NULL AS "Tertiary Family Planning Indicator"
		,NULL AS "Tertiary EPSDT / PGH Indicator"
		,NULL AS "Tertiary Insurance Claim Note"
		,NULL AS "Tertiary Insurance Claim Note"
		,NULL AS "Tertiary MSP Type"
		,NULL AS "Tertiary Emergency Treatment"
		,NULL AS "Tertiary Insurance Internal  Use/Note"
		,NULL AS "Statement Hold"
		,NULL AS "hold reason"
		,NULL AS "Stop claims"
		,NULL AS "Reason"
		,NULL AS "Stop Charges"
		,NULL AS "Reason"
		,NULL AS "Last statement date"
		,NULL AS "Next statement remark code"
	FROM
		tblPatient P 
		INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0 
		INNER JOIN tblGender P_G ON P.gender_id = P_G.gender_id  
		INNER JOIN tblEncounter ENCOUNTER ON ENCOUNTER.patient_id = P.patient_id AND ENCOUNTER.is_deleted = 0
		INNER JOIN tblAccount ACCOUNT ON ACCOUNT.account_id = ENCOUNTER.account_id AND ACCOUNT.is_deleted = 0
		INNER JOIN tblContact ACC_C ON ACCOUNT.contact_id = ACC_C.contact_id AND ACC_C.is_deleted = 0 
		INNER JOIN (
			SELECT
				INNER_ENC_PAY.encounter_id
				,INNER_PAY_P.*
			FROM
				tblEncounterPayer INNER_ENC_PAY
				INNER JOIN tblPayer INNER_PAY_P ON INNER_ENC_PAY.payer_id = INNER_PAY_P.payer_id AND INNER_PAY_P.coverage_order = 1 AND INNER_PAY_P.is_deleted = 0 
			WHERE
				INNER_ENC_PAY.is_deleted = 0
		) AS PAY_P ON PAY_P.encounter_id = ENCOUNTER.encounter_id
		LEFT OUTER JOIN (
			SELECT
				INNER_ENC_PAY_S.encounter_id
				,INNER_PAY_S.*
			FROM
				tblEncounterPayer INNER_ENC_PAY_S
				INNER JOIN tblPayer INNER_PAY_S ON INNER_ENC_PAY_S.payer_id = INNER_PAY_S.payer_id AND INNER_PAY_S.coverage_order = 2 AND INNER_PAY_S.is_deleted = 0 
			WHERE
				INNER_ENC_PAY_S.is_deleted = 0
		) AS PAY_S ON PAY_S.encounter_id = ENCOUNTER.encounter_id
		LEFT OUTER JOIN tblSample SAMPLE ON ENCOUNTER.encounter_id = SAMPLE.encounter_id AND SAMPLE.is_deleted = 0
		LEFT OUTER JOIN tblContact PAY_P_SUB_C ON PAY_P_SUB_C.contact_id = PAY_P.subscriber_contact_id AND PAY_P_SUB_C.is_deleted = 0 
		LEFT OUTER JOIN tblAddress P_C_A ON ENCOUNTER.patient_address_id = P_C_A.address_id AND P_C_A.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PH ON P.contact_id = P_C_PH.contact_id AND P_C_PH.phone_name = "Home" AND P_C_PH.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PW ON P.contact_id = P_C_PW.contact_id AND P_C_PW.phone_name = "Work" AND P_C_PW.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PO ON P.contact_id = P_C_PO.contact_id AND P_C_PO.phone_name = "Mobile" AND P_C_PO.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAY_S_SUB_C ON PAY_S_SUB_C.contact_id = PAY_S.subscriber_contact_id  AND PAY_S_SUB_C.is_deleted = 0 
		LEFT OUTER JOIN tblClinic CLINIC ON CLINIC.clinic_id = ENCOUNTER.clinic_id AND CLINIC.is_deleted = 0 
		LEFT OUTER JOIN tblContact CLINIC_C ON CLINIC_C.contact_id = CLINIC.contact_id  AND CLINIC_C.is_deleted = 0 
		LEFT OUTER JOIN tblDoctor DOCTOR ON DOCTOR.doctor_id = ENCOUNTER.doctor_id AND DOCTOR.is_deleted = 0 
		LEFT OUTER JOIN tblContact DOCTOR_C ON DOCTOR_C.contact_id = DOCTOR.contact_id AND DOCTOR_C.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAY_WC_C ON PAY_P.workers_comp_employer_contact_id = PAY_WC_C.contact_id AND PAY_WC_C.is_deleted = 0 
		LEFT OUTER JOIN tblAddress PAY_WC_C_A ON PAY_P.workers_comp_employer_contact_id = PAY_WC_C_A.contact_id AND PAY_WC_C_A.is_deleted = 0
		LEFT OUTER JOIN tblPayerCompany PAYCO_P ON PAYCO_P.payer_company_id = PAY_P.payer_company_id AND PAYCO_P.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAYCO_P_C ON PAYCO_P_C.contact_id = PAYCO_P.contact_id AND PAYCO_P_C.is_deleted = 0 
		LEFT OUTER JOIN tblPayerCompany PAYCO_S ON PAYCO_S.payer_company_id = PAY_S.payer_company_id AND PAYCO_S.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAYCO_S_C ON PAYCO_S_C.contact_id = PAYCO_S.contact_id AND PAYCO_S_C.is_deleted = 0 
		LEFT OUTER JOIN tblTest TEST ON TEST.encounter_id = ENCOUNTER.encounter_id AND TEST.is_deleted = 0 
	WHERE
		P.is_deleted = 0
		AND
		TEST.billing_download_id = new_billing_download_id
	GROUP BY P.patient_id 
	ORDER BY PAY_P.modified_datetime DESC; 
	END;
	END IF;

END
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
DROP PROCEDURE IF EXISTS pr_Billing_GetPatientDownload;

CREATE PROCEDURE `pr_Billing_GetPatientDownload`(
	IN new_session VARCHAR(255),
	IN new_billing_download_id  INT UNSIGNED,
	IN new_payer_type VARCHAR(255),
	IN new_start_date DATETIME,
	IN new_end_date DATETIME
)
this:BEGIN
	DECLARE today_cutoff DATE DEFAULT CAST(CONVERT_TZ(NOW(),'+0:00','-8:00') AS DATE);
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

	
	IF (new_start_date IS NULL OR new_end_date IS NULL) THEN
	BEGIN
		SET new_start_date := (SELECT last_download_datetime FROM tblBillingDownloadPatient ORDER BY last_download_datetime DESC LIMIT 1);

		IF (EXISTS (SELECT 1 FROM tblDatabaseConfiguration WHERE database_configuration_id = 2 and configuration_value = 1)) THEN
			SET new_end_date := NOW();
		ELSE
			SET new_end_date := today_cutoff;
		END IF;

		TRUNCATE TABLE tblBillingDownloadPatient;
		INSERT INTO tblBillingDownloadPatient(last_download_datetime) VALUE (new_end_date);
	END;
	END IF;

	
	IF (new_billing_download_id IS NULL) THEN
	BEGIN

	SELECT
		 "TOR" AS "Practice Code" 
		,COALESCE(P.external_patient_id, SAMPLE.external_sample_id) AS "Patient Account Number" 
		,CONCAT(P_C.last_name, ", ", P_C.first_name) AS "Patient Name" 
		,P_C_A.street AS "Patient Address 1" 
		,P_C_A.street2 AS "Patient Address 2"
		,CONCAT(P_C_A.city, ", ", P_C_A.state) AS "Patient City, State" 
		,P_C_A.zip AS "Patient Zip Code" 
		,CONCAT(P_C_PH.area_code, P_C_PH.local_number) AS "Patient Home Phone"
		,P_C_PH.extension AS "Patient Home Phone Ext"
		,CONCAT(P_C_PW.area_code, P_C_PW.local_number) AS "Patient Work Phone"
		,P_C_PW.extension AS "Patient Work Phone Ext"
		,CONCAT(P_C_PO.area_code, P_C_PO.local_number) AS "Patient Other Phone"
		,P_C_PO.extension AS "Patient Other Phone Ext"
		,P.birth_date AS "Patient DOB" 
		,NULL AS "Patient Work/School Status"
		,P.social_security_num AS "Patient SSN"
		,P_G.gender_name AS "Patient Gender" 
		,NULL AS "Patient Marital Status"
		,PAY_WC_C.company_name AS "Patient Employer Name"
		,CONCAT(PAY_WC_C_A.street, PAY_WC_C_A.street2) AS "Patient Employer Address"
		,CONCAT(PAY_WC_C_A.city, ", ", PAY_WC_C_A.state)  AS "Patient Employer City, State"
		,PAY_WC_C_A.zip AS "Patient Employer Zip Code"
		,P_C.email AS "Patient Email Address"
		,NULL AS "Medical Record Number"
		,NULL AS "Drivers License Number"
		,"PROOVE" AS "Provider" 
		
		,CASE
			WHEN ENCOUNTER.clinic_id IS NOT NULL THEN CONCAT(ACC_C.company_name,"; Location: ",CLINIC_C.company_name)
			ELSE ACC_C.company_name
		END AS "Location" 
		
		,CONCAT(DOCTOR_C.last_name, ", ", DOCTOR_C.first_name) AS "Referring Physician" 
		,CASE
		 	WHEN PAYCO_P.payer_type_id = 6 THEN "WC" 
		 	WHEN PAYCO_P.payer_type_id = 4 THEN "MCD" 
		 	WHEN PAYCO_P.payer_type_id = 2 THEN "MC" 
		 	WHEN PAYCO_P.payer_type_id = 8 THEN "MVA" 
		 	WHEN LOWER(PAYCO_P_C.company_name) RLIKE "((bcbs)|(blue(.)?cross(.)?blue(.)?shield))" THEN "BCBS" 
		 	WHEN PAYCO_P.payer_type_id = 5 THEN "CI"  
		 	WHEN PAYCO_P.payer_type_id = 9 THEN "TRICARE"  
		 	ELSE "UNKNOWN"
		 END AS "Financial Class"
		,NULL AS "Guarantor Name"
		,NULL AS "Guarantor Address 1"
		,NULL AS "Guarantor Address 2"
		,NULL AS "Guarantor City, State"
		,NULL AS "Guarantor Zip Code"
		,NULL AS "Guarantor Home Phone"
		,NULL AS "Guarantor Home Phone Ext"
		,NULL AS "Guarantor Work Phone"
		,NULL AS "Guarantor Work Phone Ext"
		,NULL AS "Guarantor Other Phone"
		,NULL AS "Guarantor Other Phone Ext"
		,NULL AS "Guarantor DOB"
		,NULL AS "Guarantor Gender"
		,NULL AS "Guarantor Employer Name"
		,NULL AS "Guarantor Employer Address"
		,NULL AS "Guarantor Employer City, State"
		,NULL AS "Guarantor Employer Zip"
		,NULL AS "Guarantor Email Address"
		,NULL AS "Guarantor SSN"
		,NULL AS "Guarantor Marital Status"
		,NULL AS "Emergency Contact"
		,NULL AS "Primary Care Physician"
		,NULL AS "Family Physician"
		,NULL AS "First Symptom Date"
		,NULL AS "First Treated Date"
		,NULL AS "Last Treated Date by Referrer"
		,NULL AS "Last Admission Date"
		,NULL AS "Last Discharge Date"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL WHEN PAYCO_P.payer_type_id = 6 THEN "W" ELSE "P" END AS "Primary Insurance Status (P/S/T/I/O/A/WH)" 
		,NULL AS "Primary Insurance Company Code" 
		,PAYCO_P_C.company_name AS "Primary Insurance Company Name" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE
			CASE WHEN PAYCO_P.payer_type_id = 6 THEN P.social_security_num ELSE PAY_P.policy_num END 
		END AS "Primary Insurance Policy Number" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE PAY_P.group_num END AS "Primary Insurance Group Number"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE "Y" END AS "Primary Insurance Assignment" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE CONCAT(PAY_P_SUB_C.last_name, ", ", PAY_P_SUB_C.first_name) END AS "Primary Insured Name"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE PAY_P.subscriber_relationship_type_id END AS "Primary Patient Relation to Insured" 
		,NULL AS "Primary Insured Gender"
		,NULL AS "Primary Insured Birth"
		,NULL AS "Primary Insured Address"
		,NULL AS "Primary Insured City, State"
		,NULL AS "Primary Insured Zip"
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_WC_C.company_name ELSE NULL
		END AS "Primary Insured Employer" 
		,NULL AS "Primary Insured Telephone Home"
		,NULL AS "Primary Insured Telephone Work"
		,NULL AS "Primary Insurance Plan Name"
		,NULL AS "Primary Copay Amount"
		,NULL AS "Primary Effective Date Start"
		,NULL AS "Primary Effective Date End"
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN "WC" ELSE NULL
		END AS "Primary Accident Type WAOP" 
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_P.workers_comp_injury_date ELSE NULL
		END AS "Primary Accident Date" 
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_WC_C_A.state ELSE NULL 
		END AS "Primary Accident State" 
		,NULL AS "Primary Insurance Authorization Number for Treatment"
		,NULL AS "Primary PPO / HMO"
		,NULL AS "Primary Family Planning Indicator"
		,NULL AS "Primary EPSDT / PGH Indicator"
		,NULL AS "Primary Insurance Claim Note"
		,NULL AS "Primary Insurance Claim Note"
		,NULL AS "Primary MSP Type"
		,NULL AS "Primary Emergency Treatment"
		,NULL AS "Primary Insurance Internal  Use/Note"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE "S" END AS "Secondary Insurance Status (P/S/T/I/O/A/WH)"
		,NULL AS "Secondary Insurance Company Code" 
		,PAYCO_S_C.company_name AS "Secondary Insurance Company Name"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.policy_num END AS "Secondary Insurance Policy Number"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.group_num END AS "Secondary Insurance Group Number"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE "Y" END AS "Secondary Insurance Assignment"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE CONCAT(PAY_S_SUB_C.last_name, ", ", PAY_S_SUB_C.first_name) END AS "Secondary Insured Name"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.subscriber_relationship_type_id END AS "Secondary Patient Relation to Insured"
		,NULL AS "Secondary Insured Gender"
		,NULL AS "Secondary Insured Birth"
		,NULL AS "Secondary Insured Address"
		,NULL AS "Secondary Insured City, State"
		,NULL AS "Secondary Insured Zip"
		,NULL AS "Secondary Insured Employer"
		,NULL AS "Secondary Insured Telephone Home"
		,NULL AS "Secondary Insured Telephone Work"
		,NULL AS "Secondary Insurance Plan Name"
		,NULL AS "Secondary Copay Amount"
		,NULL AS "Secondary Effective Date Start"
		,NULL AS "Secondary Effective Date End"
		,NULL AS "Secondary Accident Type WAOP"
		,NULL AS "Secondary Accident Date"
		,NULL AS "Secondary Accident State"
		,NULL AS "Secondary Insurance Authorization Number for Treatment"
		,NULL AS "Secondary PPO / HMO"
		,NULL AS "Secondary Family Planning Indicator"
		,NULL AS "Secondary EPSDT / PGH Indicator"
		,NULL AS "Secondary Insurance Claim Note"
		,NULL AS "Secondary Insurance Claim Note"
		,NULL AS "Secondary MSP Type"
		,NULL AS "Secondary Emergency Treatment"
		,NULL AS "Secondary Insurance Internal  Use/Note"
		,NULL AS "Tertiary Insurance Status (P/S/T/I/O/A/WH)"
		,NULL AS "Tertiary Insurance Company Code"
		,NULL AS "Tertiary Insurance Company Name"
		,NULL AS "Tertiary Insurance Policy Number"
		,NULL AS "Tertiary Insurance Group Number"
		,NULL AS "Tertiary Insurance Assignment"
		,NULL AS "Tertiary Insured Name"
		,NULL AS "Tertiary Patient Relation to Insured"
		,NULL AS "Tertiary Insured Gender"
		,NULL AS "Tertiary Insured Birth"
		,NULL AS "Tertiary Insured Address"
		,NULL AS "Tertiary Insured City, State"
		,NULL AS "Tertiary Insured Zip"
		,NULL AS "Tertiary Insured Employer"
		,NULL AS "Tertiary Insured Telephone Home"
		,NULL AS "Tertiary Insured Telephone Work"
		,NULL AS "Tertiary Insurance Plan Name"
		,NULL AS "Tertiary Copay Amount"
		,NULL AS "Tertiary Effective Date Start"
		,NULL AS "Tertiary Effective Date End"
		,NULL AS "Tertiary Accident Type WAOP"
		,NULL AS "Tertiary Accident Date"
		,NULL AS "Tertiary Accident State"
		,NULL AS "Tertiary Insurance Authorization Number for Treatment"
		,NULL AS "Tertiary PPO / HMO"
		,NULL AS "Tertiary Family Planning Indicator"
		,NULL AS "Tertiary EPSDT / PGH Indicator"
		,NULL AS "Tertiary Insurance Claim Note"
		,NULL AS "Tertiary Insurance Claim Note"
		,NULL AS "Tertiary MSP Type"
		,NULL AS "Tertiary Emergency Treatment"
		,NULL AS "Tertiary Insurance Internal  Use/Note"
		,NULL AS "Statement Hold"
		,NULL AS "hold reason"
		,NULL AS "Stop claims"
		,NULL AS "Reason"
		,NULL AS "Stop Charges"
		,NULL AS "Reason"
		,NULL AS "Last statement date"
		,NULL AS "Next statement remark code"
	FROM
		tblPatient P 
		INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0 
		INNER JOIN tblGender P_G ON P.gender_id = P_G.gender_id  
		INNER JOIN tblEncounter ENCOUNTER ON ENCOUNTER.patient_id = P.patient_id AND ENCOUNTER.is_deleted = 0
		INNER JOIN tblAccount ACCOUNT ON ACCOUNT.account_id = ENCOUNTER.account_id AND ACCOUNT.is_deleted = 0
		INNER JOIN tblContact ACC_C ON ACCOUNT.contact_id = ACC_C.contact_id AND ACC_C.is_deleted = 0 
		INNER JOIN (
			SELECT
				INNER_ENC_PAY.encounter_id
				,INNER_PAY_P.*
			FROM
				tblEncounterPayer INNER_ENC_PAY
				INNER JOIN tblPayer INNER_PAY_P ON INNER_ENC_PAY.payer_id = INNER_PAY_P.payer_id AND INNER_PAY_P.coverage_order = 1 AND INNER_PAY_P.is_deleted = 0 
			WHERE
				INNER_ENC_PAY.is_deleted = 0
		) AS PAY_P ON PAY_P.encounter_id = ENCOUNTER.encounter_id
		LEFT OUTER JOIN (
			SELECT
				INNER_ENC_PAY_S.encounter_id
				,INNER_PAY_S.*
			FROM
				tblEncounterPayer INNER_ENC_PAY_S
				INNER JOIN tblPayer INNER_PAY_S ON INNER_ENC_PAY_S.payer_id = INNER_PAY_S.payer_id AND INNER_PAY_S.coverage_order = 2 AND INNER_PAY_S.is_deleted = 0 
			WHERE
				INNER_ENC_PAY_S.is_deleted = 0
		) AS PAY_S ON PAY_S.encounter_id = ENCOUNTER.encounter_id
		LEFT OUTER JOIN tblSample SAMPLE ON ENCOUNTER.encounter_id = SAMPLE.encounter_id AND SAMPLE.is_deleted = 0
		LEFT OUTER JOIN tblContact PAY_P_SUB_C ON PAY_P_SUB_C.contact_id = PAY_P.subscriber_contact_id AND PAY_P_SUB_C.is_deleted = 0 
		LEFT OUTER JOIN tblAddress P_C_A ON P.contact_id = P_C_A.contact_id AND P_C_A.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PH ON P.contact_id = P_C_PH.contact_id AND P_C_PH.phone_name = "Home" AND P_C_PH.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PW ON P.contact_id = P_C_PW.contact_id AND P_C_PW.phone_name = "Work" AND P_C_PW.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PO ON P.contact_id = P_C_PO.contact_id AND P_C_PO.phone_name = "Mobile" AND P_C_PO.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAY_S_SUB_C ON PAY_S_SUB_C.contact_id = PAY_S.subscriber_contact_id  AND PAY_S_SUB_C.is_deleted = 0 
		LEFT OUTER JOIN tblClinic CLINIC ON CLINIC.clinic_id = ENCOUNTER.clinic_id AND CLINIC.is_deleted = 0 
		LEFT OUTER JOIN tblContact CLINIC_C ON CLINIC_C.contact_id = CLINIC.contact_id  AND CLINIC_C.is_deleted = 0 
		LEFT OUTER JOIN tblDoctor DOCTOR ON DOCTOR.doctor_id = ENCOUNTER.doctor_id AND DOCTOR.is_deleted = 0 
		LEFT OUTER JOIN tblContact DOCTOR_C ON DOCTOR_C.contact_id = DOCTOR.contact_id AND DOCTOR_C.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAY_WC_C ON PAY_P.workers_comp_employer_contact_id = PAY_WC_C.contact_id AND PAY_WC_C.is_deleted = 0 
		LEFT OUTER JOIN tblAddress PAY_WC_C_A ON PAY_P.workers_comp_employer_contact_id = PAY_WC_C_A.contact_id AND PAY_WC_C_A.is_deleted = 0
		LEFT OUTER JOIN tblPayerCompany PAYCO_P ON PAYCO_P.payer_company_id = PAY_P.payer_company_id AND PAYCO_P.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAYCO_P_C ON PAYCO_P_C.contact_id = PAYCO_P.contact_id AND PAYCO_P_C.is_deleted = 0 
		LEFT OUTER JOIN tblPayerCompany PAYCO_S ON PAYCO_S.payer_company_id = PAY_S.payer_company_id AND PAYCO_S.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAYCO_S_C ON PAYCO_S_C.contact_id = PAYCO_S.contact_id AND PAYCO_S_C.is_deleted = 0 
		LEFT OUTER JOIN tblTest TEST ON TEST.encounter_id = ENCOUNTER.encounter_id AND TEST.is_deleted = 0 
	WHERE
		P.is_deleted = 0
		AND
		TEST.test_status_id > 3
		AND
		SAMPLE.is_preintake_needed = 0
		AND
		(
			(new_payer_type = "unknown_payer_type" AND PAYCO_P.payer_type_id = 1)
			OR
			(new_payer_type = "medicare_medicaid" AND PAYCO_P.payer_type_id IN (2, 4))
			OR
			(new_payer_type = "client_account_billing" AND PAYCO_P.payer_type_id = 3)
			OR
			(new_payer_type = "private_insurance" AND PAYCO_P.payer_type_id = 5)
			OR
			(new_payer_type = "workers_comp" AND PAYCO_P.payer_type_id = 6)
			OR
			(new_payer_type = "patient_cash" AND PAYCO_P.payer_type_id = 7)
			OR
			(new_payer_type = "patient_assistance_program" AND PAYCO_P.payer_type_id = 8)
			OR
			(new_payer_type = "tricare" AND PAYCO_P.payer_type_id = 9)
			OR
			new_payer_type IS NULL
		)
		AND
		(
			(
				(new_start_date IS NULL OR new_start_date <= P.created_datetime)
				AND
				(new_end_date IS NULL OR new_end_date >= P.created_datetime)
			)
			OR
			(
				(new_start_date IS NULL OR new_start_date <= P.modified_datetime)
				AND
				(new_end_date IS NULL OR new_end_date >= P.modified_datetime)
			)
			OR
			(
				(new_start_date IS NULL OR new_start_date <= SAMPLE.modified_datetime)
				AND
				(new_end_date IS NULL OR new_end_date >= SAMPLE.modified_datetime)
			)
			OR
			(
				(new_start_date IS NULL OR new_start_date <= TEST.modified_datetime)
				AND
				(new_end_date IS NULL OR new_end_date >= TEST.modified_datetime)
			)
		)
		AND
		(
			EXISTS (SELECT 1 FROM tblDatabaseConfiguration WHERE database_configuration_id = 2 and configuration_value = 1)
			OR
			(
				(P.modified_datetime IS NULL OR P.modified_datetime < today_cutoff)
				AND
				P.created_datetime < today_cutoff
			)
			OR
			(
				(SAMPLE.modified_datetime IS NULL OR SAMPLE.modified_datetime < today_cutoff)
				AND
				SAMPLE.created_datetime < today_cutoff
			)
			OR
			(
				(TEST.modified_datetime IS NULL OR TEST.modified_datetime < today_cutoff)
				AND
				TEST.created_datetime < today_cutoff
			)
		)
	GROUP BY P.patient_id 
	ORDER BY P.created_datetime DESC; 

	END;
	ELSE 
	BEGIN

	SELECT
		 "TOR" AS "Practice Code" 
		,COALESCE(P.external_patient_id, SAMPLE.external_sample_id) AS "Patient Account Number" 
		,CONCAT(P_C.last_name, ", ", P_C.first_name) AS "Patient Name" 
		,P_C_A.street AS "Patient Address 1" 
		,P_C_A.street2 AS "Patient Address 2"
		,CONCAT(P_C_A.city, ", ", P_C_A.state) AS "Patient City, State" 
		,P_C_A.zip AS "Patient Zip Code" 
		,CONCAT(P_C_PH.area_code, P_C_PH.local_number) AS "Patient Home Phone"
		,P_C_PH.extension AS "Patient Home Phone Ext"
		,CONCAT(P_C_PW.area_code, P_C_PW.local_number) AS "Patient Work Phone"
		,P_C_PW.extension AS "Patient Work Phone Ext"
		,CONCAT(P_C_PO.area_code, P_C_PO.local_number) AS "Patient Other Phone"
		,P_C_PO.extension AS "Patient Other Phone Ext"
		,P.birth_date AS "Patient DOB" 
		,NULL AS "Patient Work/School Status"
		,P.social_security_num AS "Patient SSN"
		,P_G.gender_name AS "Patient Gender" 
		,NULL AS "Patient Marital Status"
		,PAY_WC_C.company_name AS "Patient Employer Name"
		,CONCAT(PAY_WC_C_A.street, PAY_WC_C_A.street2) AS "Patient Employer Address"
		,CONCAT(PAY_WC_C_A.city, ", ", PAY_WC_C_A.state)  AS "Patient Employer City, State"
		,PAY_WC_C_A.zip AS "Patient Employer Zip Code"
		,P_C.email AS "Patient Email Address"
		,NULL AS "Medical Record Number"
		,NULL AS "Drivers License Number"
		,"PROOVE" AS "Provider" 
		
		,CASE
			WHEN ENCOUNTER.clinic_id IS NOT NULL THEN CONCAT(ACC_C.company_name,"; Location: ",CLINIC_C.company_name)
			ELSE ACC_C.company_name
		END AS "Location" 
		
		,CONCAT(DOCTOR_C.last_name, ", ", DOCTOR_C.first_name) AS "Referring Physician" 
		,CASE
		 	WHEN PAYCO_P.payer_type_id = 6 THEN "WC" 
		 	WHEN PAYCO_P.payer_type_id = 4 THEN "MCD" 
		 	WHEN PAYCO_P.payer_type_id = 2 THEN "MC" 
		 	WHEN PAYCO_P.payer_type_id = 8 THEN "MVA" 
		 	WHEN LOWER(PAYCO_P_C.company_name) RLIKE "((bcbs)|(blue(.)?cross(.)?blue(.)?shield))" THEN "BCBS" 
		 	WHEN PAYCO_P.payer_type_id = 5 THEN "CI"  
		 	WHEN PAYCO_P.payer_type_id = 9 THEN "TRICARE"  
		 	ELSE "UNKNOWN"
		 END AS "Financial Class"
		,NULL AS "Guarantor Name"
		,NULL AS "Guarantor Address 1"
		,NULL AS "Guarantor Address 2"
		,NULL AS "Guarantor City, State"
		,NULL AS "Guarantor Zip Code"
		,NULL AS "Guarantor Home Phone"
		,NULL AS "Guarantor Home Phone Ext"
		,NULL AS "Guarantor Work Phone"
		,NULL AS "Guarantor Work Phone Ext"
		,NULL AS "Guarantor Other Phone"
		,NULL AS "Guarantor Other Phone Ext"
		,NULL AS "Guarantor DOB"
		,NULL AS "Guarantor Gender"
		,NULL AS "Guarantor Employer Name"
		,NULL AS "Guarantor Employer Address"
		,NULL AS "Guarantor Employer City, State"
		,NULL AS "Guarantor Employer Zip"
		,NULL AS "Guarantor Email Address"
		,NULL AS "Guarantor SSN"
		,NULL AS "Guarantor Marital Status"
		,NULL AS "Emergency Contact"
		,NULL AS "Primary Care Physician"
		,NULL AS "Family Physician"
		,NULL AS "First Symptom Date"
		,NULL AS "First Treated Date"
		,NULL AS "Last Treated Date by Referrer"
		,NULL AS "Last Admission Date"
		,NULL AS "Last Discharge Date"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL WHEN PAYCO_P.payer_type_id = 6 THEN "W" ELSE "P" END AS "Primary Insurance Status (P/S/T/I/O/A/WH)" 
		,NULL 
		,PAYCO_P_C.company_name AS "Primary Insurance Company Name" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE
			CASE WHEN PAYCO_P.payer_type_id = 6 THEN P.social_security_num ELSE PAY_P.policy_num END 
		END AS "Primary Insurance Policy Number" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE PAY_P.group_num END AS "Primary Insurance Group Number"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE "Y" END AS "Primary Insurance Assignment" 
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE CONCAT(PAY_P_SUB_C.last_name, ", ", PAY_P_SUB_C.first_name) END AS "Primary Insured Name"
		,CASE WHEN PAY_P.payer_company_id IS NULL THEN NULL ELSE PAY_P.subscriber_relationship_type_id END AS "Primary Patient Relation to Insured" 
		,NULL AS "Primary Insured Gender"
		,NULL AS "Primary Insured Birth"
		,NULL AS "Primary Insured Address"
		,NULL AS "Primary Insured City, State"
		,NULL AS "Primary Insured Zip"
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_WC_C.company_name ELSE NULL
		END AS "Primary Insured Employer" 
		,NULL AS "Primary Insured Telephone Home"
		,NULL AS "Primary Insured Telephone Work"
		,NULL AS "Primary Insurance Plan Name"
		,NULL AS "Primary Copay Amount"
		,NULL AS "Primary Effective Date Start"
		,NULL AS "Primary Effective Date End"
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN "WC" ELSE NULL
		END AS "Primary Accident Type WAOP" 
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_P.workers_comp_injury_date ELSE NULL
		END AS "Primary Accident Date" 
		,CASE
			WHEN PAYCO_P.payer_type_id = 6 THEN PAY_WC_C_A.state ELSE NULL 
		END AS "Primary Accident State" 
		,NULL AS "Primary Insurance Authorization Number for Treatment"
		,NULL AS "Primary PPO / HMO"
		,NULL AS "Primary Family Planning Indicator"
		,NULL AS "Primary EPSDT / PGH Indicator"
		,NULL AS "Primary Insurance Claim Note"
		,NULL AS "Primary Insurance Claim Note"
		,NULL AS "Primary MSP Type"
		,NULL AS "Primary Emergency Treatment"
		,NULL AS "Primary Insurance Internal  Use/Note"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE "S" END AS "Secondary Insurance Status (P/S/T/I/O/A/WH)"
		,NULL 
		,PAYCO_S_C.company_name AS "Secondary Insurance Company Name"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.policy_num END AS "Secondary Insurance Policy Number"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.group_num END AS "Secondary Insurance Group Number"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE "Y" END AS "Secondary Insurance Assignment"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE CONCAT(PAY_S_SUB_C.last_name, ", ", PAY_S_SUB_C.first_name) END AS "Secondary Insured Name"
		,CASE WHEN PAY_S.payer_company_id IS NULL THEN NULL ELSE PAY_S.subscriber_relationship_type_id END AS "Secondary Patient Relation to Insured"
		,NULL AS "Secondary Insured Gender"
		,NULL AS "Secondary Insured Birth"
		,NULL AS "Secondary Insured Address"
		,NULL AS "Secondary Insured City, State"
		,NULL AS "Secondary Insured Zip"
		,NULL AS "Secondary Insured Employer"
		,NULL AS "Secondary Insured Telephone Home"
		,NULL AS "Secondary Insured Telephone Work"
		,NULL AS "Secondary Insurance Plan Name"
		,NULL AS "Secondary Copay Amount"
		,NULL AS "Secondary Effective Date Start"
		,NULL AS "Secondary Effective Date End"
		,NULL AS "Secondary Accident Type WAOP"
		,NULL AS "Secondary Accident Date"
		,NULL AS "Secondary Accident State"
		,NULL AS "Secondary Insurance Authorization Number for Treatment"
		,NULL AS "Secondary PPO / HMO"
		,NULL AS "Secondary Family Planning Indicator"
		,NULL AS "Secondary EPSDT / PGH Indicator"
		,NULL AS "Secondary Insurance Claim Note"
		,NULL AS "Secondary Insurance Claim Note"
		,NULL AS "Secondary MSP Type"
		,NULL AS "Secondary Emergency Treatment"
		,NULL AS "Secondary Insurance Internal  Use/Note"
		,NULL AS "Tertiary Insurance Status (P/S/T/I/O/A/WH)"
		,NULL AS "Tertiary Insurance Company Code"
		,NULL AS "Tertiary Insurance Company Name"
		,NULL AS "Tertiary Insurance Policy Number"
		,NULL AS "Tertiary Insurance Group Number"
		,NULL AS "Tertiary Insurance Assignment"
		,NULL AS "Tertiary Insured Name"
		,NULL AS "Tertiary Patient Relation to Insured"
		,NULL AS "Tertiary Insured Gender"
		,NULL AS "Tertiary Insured Birth"
		,NULL AS "Tertiary Insured Address"
		,NULL AS "Tertiary Insured City, State"
		,NULL AS "Tertiary Insured Zip"
		,NULL AS "Tertiary Insured Employer"
		,NULL AS "Tertiary Insured Telephone Home"
		,NULL AS "Tertiary Insured Telephone Work"
		,NULL AS "Tertiary Insurance Plan Name"
		,NULL AS "Tertiary Copay Amount"
		,NULL AS "Tertiary Effective Date Start"
		,NULL AS "Tertiary Effective Date End"
		,NULL AS "Tertiary Accident Type WAOP"
		,NULL AS "Tertiary Accident Date"
		,NULL AS "Tertiary Accident State"
		,NULL AS "Tertiary Insurance Authorization Number for Treatment"
		,NULL AS "Tertiary PPO / HMO"
		,NULL AS "Tertiary Family Planning Indicator"
		,NULL AS "Tertiary EPSDT / PGH Indicator"
		,NULL AS "Tertiary Insurance Claim Note"
		,NULL AS "Tertiary Insurance Claim Note"
		,NULL AS "Tertiary MSP Type"
		,NULL AS "Tertiary Emergency Treatment"
		,NULL AS "Tertiary Insurance Internal  Use/Note"
		,NULL AS "Statement Hold"
		,NULL AS "hold reason"
		,NULL AS "Stop claims"
		,NULL AS "Reason"
		,NULL AS "Stop Charges"
		,NULL AS "Reason"
		,NULL AS "Last statement date"
		,NULL AS "Next statement remark code"
	FROM
		tblPatient P 
		INNER JOIN tblContact P_C ON P.contact_id = P_C.contact_id AND P_C.is_deleted = 0 
		INNER JOIN tblGender P_G ON P.gender_id = P_G.gender_id  
		INNER JOIN tblEncounter ENCOUNTER ON ENCOUNTER.patient_id = P.patient_id AND ENCOUNTER.is_deleted = 0
		INNER JOIN tblAccount ACCOUNT ON ACCOUNT.account_id = ENCOUNTER.account_id AND ACCOUNT.is_deleted = 0
		INNER JOIN tblContact ACC_C ON ACCOUNT.contact_id = ACC_C.contact_id AND ACC_C.is_deleted = 0 
		INNER JOIN (
			SELECT
				INNER_ENC_PAY.encounter_id
				,INNER_PAY_P.*
			FROM
				tblEncounterPayer INNER_ENC_PAY
				INNER JOIN tblPayer INNER_PAY_P ON INNER_ENC_PAY.payer_id = INNER_PAY_P.payer_id AND INNER_PAY_P.coverage_order = 1 AND INNER_PAY_P.is_deleted = 0 
			WHERE
				INNER_ENC_PAY.is_deleted = 0
		) AS PAY_P ON PAY_P.encounter_id = ENCOUNTER.encounter_id
		LEFT OUTER JOIN (
			SELECT
				INNER_ENC_PAY_S.encounter_id
				,INNER_PAY_S.*
			FROM
				tblEncounterPayer INNER_ENC_PAY_S
				INNER JOIN tblPayer INNER_PAY_S ON INNER_ENC_PAY_S.payer_id = INNER_PAY_S.payer_id AND INNER_PAY_S.coverage_order = 2 AND INNER_PAY_S.is_deleted = 0 
			WHERE
				INNER_ENC_PAY_S.is_deleted = 0
		) AS PAY_S ON PAY_S.encounter_id = ENCOUNTER.encounter_id
		LEFT OUTER JOIN tblSample SAMPLE ON ENCOUNTER.encounter_id = SAMPLE.encounter_id AND SAMPLE.is_deleted = 0
		LEFT OUTER JOIN tblContact PAY_P_SUB_C ON PAY_P_SUB_C.contact_id = PAY_P.subscriber_contact_id AND PAY_P_SUB_C.is_deleted = 0 
		LEFT OUTER JOIN tblAddress P_C_A ON P.contact_id = P_C_A.contact_id AND P_C_A.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PH ON P.contact_id = P_C_PH.contact_id AND P_C_PH.phone_name = "Home" AND P_C_PH.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PW ON P.contact_id = P_C_PW.contact_id AND P_C_PW.phone_name = "Work" AND P_C_PW.is_deleted = 0 
		LEFT OUTER JOIN tblPhone P_C_PO ON P.contact_id = P_C_PO.contact_id AND P_C_PO.phone_name = "Mobile" AND P_C_PO.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAY_S_SUB_C ON PAY_S_SUB_C.contact_id = PAY_S.subscriber_contact_id  AND PAY_S_SUB_C.is_deleted = 0 
		LEFT OUTER JOIN tblClinic CLINIC ON CLINIC.clinic_id = ENCOUNTER.clinic_id AND CLINIC.is_deleted = 0 
		LEFT OUTER JOIN tblContact CLINIC_C ON CLINIC_C.contact_id = CLINIC.contact_id  AND CLINIC_C.is_deleted = 0 
		LEFT OUTER JOIN tblDoctor DOCTOR ON DOCTOR.doctor_id = ENCOUNTER.doctor_id AND DOCTOR.is_deleted = 0 
		LEFT OUTER JOIN tblContact DOCTOR_C ON DOCTOR_C.contact_id = DOCTOR.contact_id AND DOCTOR_C.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAY_WC_C ON PAY_P.workers_comp_employer_contact_id = PAY_WC_C.contact_id AND PAY_WC_C.is_deleted = 0 
		LEFT OUTER JOIN tblAddress PAY_WC_C_A ON PAY_P.workers_comp_employer_contact_id = PAY_WC_C_A.contact_id AND PAY_WC_C_A.is_deleted = 0
		LEFT OUTER JOIN tblPayerCompany PAYCO_P ON PAYCO_P.payer_company_id = PAY_P.payer_company_id AND PAYCO_P.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAYCO_P_C ON PAYCO_P_C.contact_id = PAYCO_P.contact_id AND PAYCO_P_C.is_deleted = 0 
		LEFT OUTER JOIN tblPayerCompany PAYCO_S ON PAYCO_S.payer_company_id = PAY_S.payer_company_id AND PAYCO_S.is_deleted = 0 
		LEFT OUTER JOIN tblContact PAYCO_S_C ON PAYCO_S_C.contact_id = PAYCO_S.contact_id AND PAYCO_S_C.is_deleted = 0 
		LEFT OUTER JOIN tblTest TEST ON TEST.encounter_id = ENCOUNTER.encounter_id AND TEST.is_deleted = 0 
	WHERE
		P.is_deleted = 0
		AND
		TEST.billing_download_id = new_billing_download_id
	GROUP BY P.patient_id 
	ORDER BY PAY_P.modified_datetime DESC; 
	END;
	END IF;

END
EOT;
		$this->querySQL($sql);
	}

}
