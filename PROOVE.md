Proove Version of SFTW, the DB Migration Tool
=============================================

Proove version of *South for the Winter* ("SFTW") has some major changes based on its foundation.
First of all, it keeps records for all db migrations, instead of just maintaining the pointer of
current one. The version number is no longer a linear interger, but a datetime value in format
of YYYYMMDDHHMMSS, e.g. 20150314185002. There are also new commands to faciliate creating
migration script, as well as querying them.

One convenient new feature is that this tool will look for local environment configurations.
User doesn't have to provide database host, database name and user credential through command
options if the system already has those values set in environment.

New/Updated Commands
====================

Create
------

This command will help to create a boilerplate migration script, for example:
  
    $ ./sftw create --desc "what is this migration about"

You will get a new file under `./scripts/migrations`:

    New migration class is created: ./scripts/migrations/20150315041150-what_is_this_migration_about.php

Fill in the SQLs for both apply and de-apply db changes, save/commit file. Now user can run `latest` command to execute migrations.

```
<?php

use Dws\Sftw\Db\Schema\AbstractChange as SchemaChange;

/**
 * @author
 */
class MigrationClass_20150315041150 extends SchemaChange
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
```

Recent
------

This command will show you the most recent schema migrations that database logged. Argument is default to 1. Latest migration on the top.
  
    $ ./sftw recent 3

You may get output like following:

```
Recent version: 20150314185002    2015-03-15 03:32:29    20150314185002-add_new_column_in_user_table.php
Recent version: 20150314184138    2015-03-15 03:32:29    20150314184138-Migration_Test_1.php
Recent version: 20150313192649    2015-03-15 01:44:27    20150313192649-Test.php
```
