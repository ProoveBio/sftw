<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150918201402 extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
ALTER TABLE `tblAccount` ADD `bucket` TINYINT NULL DEFAULT NULL AFTER `sf_ra_user_id`;
EOT;
		$this->querySQL($sql);

		foreach(self::$bucketValuesForCurrentActiveAccounts as $sf_account_id => $bucket) {
		    $this->querySQL("UPDATE `tblAccount` SET `bucket` = '{$bucket}' WHERE `sf_account_id` LIKE '{$sf_account_id}%'");
		} 
	}

	public function down()
	{
		$sql = <<< EOT
ALTER TABLE `tblAccount` DROP `bucket`;
EOT;
		$this->querySQL($sql);
	}

	public static $bucketValuesForCurrentActiveAccounts = array(
	        '001E000001EdKUG' => 1,
	        '001E0000011UJM2' => 1,
	        '001E0000019nJEM' => 1,
	        '001E000001AN5WZ' => 1,
	        '001E000001AN2ax' => 1,
	        '001E000001BlzBu' => 1,
	        '001E000001Bkytv' => 1,
	        '001E000001FX0EO' => 1,
	        '001E000001EFwCb' => 1,
	        '001E000001GY21w' => 1,
	        '001E000001DPkgq' => 2,
	        '001E000001AN2Jg' => 2,
	        '001E000001EFobP' => 2,
	        '001E000001EFsSK' => 2,
	        '001E000001FV9YF' => 2,
	        '001E000001GZfxz' => 3,
	        '001E000001Hu5uN' => 3,
	        '001E000001H7gTm' => 3,
	        '001E000001HMzf6' => 3,
	        '001E000001Hv7Ze' => 3,
	        '001E000001GXYz6' => 3,
	        '001E000001HLWvI' => 3,
	        '001E000001GYaTb' => 3,
	        '001E000001GZ1mB' => 3,
	        '001E000001GZESo' => 3,
	        '001E000001H7pqk' => 3,
	        '001E000001HKt0k' => 3,
	        '001E000001HL4IV' => 3,
	        '001E000001HttOt' => 3,
	        '001E000001HuiFj' => 3,
	        '001E00000144YN0' => 3,
	        '001E000001GYFRA' => 3,
	        '001E00000147Ttx' => 3,
	        '001E000000tQtCx' => 3,
	        '001E0000012VU84' => 3,
	        '001E000001DPGcL' => 3,
	        '001E000001D3fmz' => 3,
	        '001E000000iX5Ln' => 3,
	        '001E000000iX5M7' => 3,
	        '001E0000010s4Vt' => 3,
	        '001E000000uWdio' => 3,
	        '001E000000yzsJL' => 3,
	        '001E0000010QEj1' => 3,
	        '001E000000rcaMR' => 3,
	        '001E000000w0S68' => 3,
	        '001E000000orIOb' => 3,
	        '001E0000011xopa' => 3,
	        '001E000000pJwA9' => 3,
	        '001E000000lhevi' => 3,
	        '001E000000kzTiB' => 3,
	        '001E000000mIrpI' => 3,
	        '001E000001DPHHW' => 3,
	        '001E000000iX5Nf' => 3,
	        '001E000000iX5OI' => 3,
	        '001E000000iX5OO' => 3,
	        '001E000000iX5Mt' => 3,
	        '001E000000jmZlx' => 3,
	        '001E000000jOpWC' => 3,
	        '001E000000Hkayu' => 3,
	        '001E0000010rElA' => 3,
	        '001E000000tmitg' => 3,
	        '001E0000011yYkp' => 3,
	        '001E000000mI28X' => 3,
	        '001E0000010rP6U' => 3,
	        '001E000001DPHfQ' => 3,
	        '001E000001DPrYz' => 3,
	        '001E000000tP6qd' => 3,
	        '001E0000014ZO3E' => 3,
	        '001E0000014Zdcv' => 3,
	        '001E0000016GSMu' => 3,
	        '001E0000016s4tJ' => 3,
	        '001E0000016t1Sv' => 3,
	        '001E0000017wYsF' => 3,
	        '001E0000017wYIH' => 3,
	        '001E0000017yNGF' => 3,
	        '001E0000017wa7R' => 3,
	        '001E0000017wWyu' => 3,
	        '001E00000194A3U' => 3,
	        '001E000001AMzG9' => 3,
	        '001E0000019o0dD' => 3,
	        '001E0000019mzEf' => 3,
	        '001E000001Bi1Zw' => 3,
	        '001E000001B6EIf' => 3,
	        '001E000001APGvJ' => 3,
	        '001E000001B3BNi' => 3,
	        '001E000001APajc' => 3,
	        '001E000001BSchj' => 3,
	        '001E000001BiWpe' => 3,
	        '001E000001BS4Nv' => 3,
	        '001E000001APLJ0' => 3,
	        '001E000001APM5S' => 3,
	        '001E000001APWoV' => 3,
	        '001E000001APLhp' => 3,
	        '001E000001BVMnj' => 3,
	        '001E000001BV4NX' => 3,
	        '001E000001APbAK' => 3,
	        '001E000001D0bXP' => 3,
	        '001E000001BlyI2' => 3,
	        '001E000001BlCeF' => 3,
	        '001E000001BlxbZ' => 3,
	        '001E000001HN8xW' => 3,
	        '001E000001Dzuqi' => 3,
	        '001E000001EdJsm' => 3,
	        '001E000001FXRl0' => 3,
	        '001E000001EFpLO' => 3,
	        '001E000001FB8MG' => 3,
	        '001E000001EEgpn' => 3,
	        '001E000001EDSdz' => 3,
	        '001E000001Dzog5' => 3,
	        '001E000001Ee7ie' => 3,
	        '001E000001FYhbs' => 3,
	        '001E000001Fvo76' => 3,
	        '001E000001FYzgt' => 3,
	        '001E000001FWA9A' => 3,
	        '001E000001FxYDn' => 3,
	        '001E000001Fw3tC' => 3,
	        '001E000001GXoGW' => 3,
	        '001E000001GXn82' => 3,
	);

}
