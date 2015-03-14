<?php

namespace Dws\Sftw\Symfony\Component\Console\Command;

use Dws\Sftw\Db\Schema\MigrateException;

use Symfony\Component\Console;
use Dws\Sftw\Db\Schema\Manager;

/**
 * Create a migration class file boiler template
 *
 * @author Simon Yang <syang@proovebio.com>
 */
class Create extends AbstractSftw
{
    private $template_path = './scripts/class_template.tpl';

    public function __construct()
    {
		parent::__construct('create');
		$this->setDescription('Create a migration class file boiler template');
		$this->setHelp('Create a migration class file boiler template');

		$this->setDefinition([]);
		$this->addOption('desc', 'e', Console\Input\InputOption::VALUE_REQUIRED, 'Description of migration class');
		$this->addOption('path', 'f', Console\Input\InputOption::VALUE_REQUIRED, 'Path for migration files. Default: ./scripts/migrations', './scripts/migrations');
    }
	
	public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
	    $this->_checkParams($input, $output);

	    if (file_exists($this->template_path)) {
            $version = $this->_getCurrentDateTimeInUTC();
            $desc = $this->_normalizeName($input->getOption('desc'));

            $template = file_get_contents($this->template_path);
            $template = str_replace('{{classname}}', Manager::CLASS_PREFIX . $version, $template);

	        $newClass = $input->getOption('path') . DIRECTORY_SEPARATOR . $version . '-' . $desc . '.php';

	        if (false === file_put_contents($newClass, $template)) {
	            $this->errors[] = 'Failed to create migration class: ' . $newClass;
	        }
	    } else {
	        $this->errors[] = 'Boilerplate file is missing, ' . $this->template_path . ' does not exists.';
	    }

		if (count($this->errors) > 0) {
	        $this->outputErrorsAndExit($output);
	    } else {
    	    $output->writeln('New migration class is created: ' . $newClass);
	    }

		exit(0);
	}

	private function _checkParams(Console\Input\InputInterface $input, Console\Output\OutputInterface $output) {
	    if (!$input->getOption('desc')) {
	        $this->errors[] = 'Migration class description is required';
	    }
	    
	    if (!$input->getOption('path')) {
	        $this->errors[] = 'Migration script path is required';
	    }
	    
	    if (!is_dir($input->getOption('path'))) {
	        $this->errors[] = 'Unable to find migration directory: ' . $input->getOption('path');
	    }
	    
	    if (count($this->errors) > 0) {
	        $this->outputErrorsAndExit($output);
	    }
	}

	/**
	 * Get current time in YYYYMMDDHHIISS based of UTC time zone
	 * 
	 */
	private function _getCurrentDateTimeInUTC() {
	    date_default_timezone_set('America/Los_Angeles');
	    $date = new \DateTime();
	    $date->setTimezone(new \DateTimeZone('UTC'));
	    return $date->format('YmdHis');
	}

	/**
	 * Cleanup name for use as linux file name, only allow alphanumeric charaters
	 * 
	 * @param String $name
	 */
	private function _normalizeName($name) {
	    return preg_replace('/[^a-zA-Z0-9-_]+/', '_', $name);
	}
}
