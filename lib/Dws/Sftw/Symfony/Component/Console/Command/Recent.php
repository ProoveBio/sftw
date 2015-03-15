<?php

namespace Dws\Sftw\Symfony\Component\Console\Command;

use Symfony\Component\Console;

/**
 * Displays the current schema version
 *
 * @author David Weinraub <david.weinraub@diamondwebservices.com>
 */
class Recent extends AbstractSftw
{
    public function __construct()
    {
		parent::__construct('recent');
		$this->setDescription('Displays recent schema versions');
		$this->setHelp('Displays recent schema versions');

		$this->addArgument('target', Console\Input\InputArgument::OPTIONAL, 'Number of most recent migrations', 1);
    }
	
	public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		parent::execute($input, $output);

		$target = (int) $input->getArgument('target');
		$target = $target < 1 ? 1 : $target;

		$this->displayRecentSchemaVersions($target, $output);
		exit(0);
	}
}
