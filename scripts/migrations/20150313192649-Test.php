<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150313192649 extends SchemaChange
{

	public function up()
	{

// 		$sql = '
// 			CREATE TABLE `test_migrate_1` (
// 				`id` INT(11) UNSIGNED NOT NULL,
// 				`name` VARCHAR(255)
// 			)
// 		';
// 		$this->querySQL($sql);
        echo "in Up!" . PHP_EOL;
	}

	public function down()
	{
// 		$sql = 'DROP TABLE `test_migrate_1`';
// 		$this->querySQL($sql);
        echo "in Down!" . PHP_EOL;
	}

}

