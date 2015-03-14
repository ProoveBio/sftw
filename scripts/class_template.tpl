<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class {{classname}} extends SchemaChange
{

	public function up()
	{
		$sql = <<< EOT
-- Write your migration SQL here
EOT;
		$this->querySQL($sql);	
	}

	public function down()
	{
		$sql = <<< EOT
-- Write your migration SQL here
EOT;
		$this->querySQL($sql);
	}

}
