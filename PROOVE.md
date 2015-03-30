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

Latest & Migrate (Updated)
--------------------------

Introduced a new option '*--dry-run*' or '*-b*' to allow user to see what exact SQL statements going to run by the migrations.
It will not change the database schema or records, instead show SQL statements that will run.

    $ ./sftw latest --dry-run

Sample output:
```
Latest schema version: 20150317233801
Target schema version:  20150316201958
Direction:              down
Processing file: ./scripts/migrations/20150317233801-create_new_store_procedure_for_document_upsert.php
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS pr_Document_Upsert;
-- -----------------------------------------------------------------------------
Schema migrated to version 20150317233801

```

'migrate' command also accepts '*--dry-run*' option, which will output similar message as above.

    $ ./sftw migrate --dry-run 20150314184138

Also new option '*--deep-fry*' on '*-y*' is introduced to find all migrations that are not applied to the database from day one.
This happens when we merge branches of migration scripts, the current head is already moved to certain point on timeline
when certain migrations left behind the head. Using '--deep-fry' option will look for those migrations and apply them.

    $ ./sftw latest --deep-fry

Sample output:
```
Latest schema version: 0
Target schema version:  20150323190813
Direction:              up
Processing file: ./scripts/migrations/20150316201959-document_type_data.php
Processing file: ./scripts/migrations/20150323190813-Add_foreign_key_contraint_on_document_type_id.php
Schema migrated to version 20150323190813
```
Notice the first line, the 'Latest schema version' is not the current head any more. Instead it is set to 0.
