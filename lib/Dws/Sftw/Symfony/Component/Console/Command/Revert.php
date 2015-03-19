<?php

namespace Dws\Sftw\Symfony\Component\Console\Command;

use Dws\Sftw\Db\Schema\MigrateException;
use Symfony\Component\Console;

/**
 * Runs the down for one specific migrate
 *
 * @author Brian Imbach
 */
class Revert extends AbstractSftw
{
    public function __construct()
    {
		parent::__construct('revert');
		$this->setDescription('Runs single down migration of the specified version');
		$this->setHelp('Run single down migration of the specified version');
		
		$this->addArgument('target', Console\Input\InputArgument::REQUIRED, 'The desired schema version');
    }
	
	public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		parent::execute($input, $output);
						
		$target = $input->getArgument('target');

		$currentVersions = $this->manager->getCurrentSchemaVersions();
		if (!in_array($target,$currentVersions)){
			echo "The specified migration has not been run and therefore can't be reverted\n";
			exit(0);
		}

		try {
			$result = $this->manager->runSingle($target,'down');		
		} catch (MigrateException $e) {
			$this->errors[] = $e->getMessage();
			$this->outputErrorsAndExit($output, 1);
		}

		$version = $this->manager->getCurrentSchemaVersion();
		$this->outputResult($result, $version, $output);
		exit(0);
	}
}
