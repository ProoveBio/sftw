<?php

namespace Dws\Sftw\Symfony\Component\Console\Command;

use Dws\Sftw\Db\Schema\Manager as SchemaManager;
use Dws\Sftw\Util\Dsn as DsnUtil;
use PDO;
use Symfony\Component\Console;

/**
 * An abstract base for SFTW commands
 *
 * @author David Weinraub <david.weinraub@diamondwebservices.com>
 */
abstract class AbstractSftw extends Console\Command\Command
{

	/**
	 * The schema manager
	 * 
	 * @var \Dws\Sftw\\Db\Schema\Manager
	 */
	protected $manager;
	
	/**
	 *
	 * @var type 
	 */
	protected $driver;
	protected $host;
	protected $user;
	protected $pass;
	protected $db;
	protected $path;
	protected $namespace;
		
	protected $errors = array();

	/**
	 * @var Environment config file path
	 */
	private static $envpath = '/etc/helix/rconfig.json';
	
	/**
	 * Construct
	 */
	public function __construct($name = null)
	{
		parent::__construct($name);
		
		/**
		 * Define CLI options.
		 * 
		 * Symfony reserves -h and -n options, so there are some counter-intuitive ones 
		 * in here, like -c for host ("connection'), -m for namespace, etc.
		 */
        $this->addOption('host', 'c', Console\Input\InputOption::VALUE_REQUIRED, 'DB host/connection');
        $this->addOption('user', 'u', Console\Input\InputOption::VALUE_REQUIRED, 'DB user');
        $this->addOption('pass', 'p', Console\Input\InputOption::VALUE_REQUIRED, 'DB password');
        $this->addOption('db', 'd', Console\Input\InputOption::VALUE_REQUIRED, 'DB name');
        $this->addOption('dsn', 's', Console\Input\InputOption::VALUE_REQUIRED, 'Complete DSN string. Overrides individual host/user/pass/db values. Format: mysql://john:pass@localhost:port/my_db', '');
        $this->addOption('namespace', 'm', Console\Input\InputOption::VALUE_REQUIRED, 'Namespace for the migration classes (forward slashes ok, will be transformed)');
        $this->addOption('path', 'f', Console\Input\InputOption::VALUE_REQUIRED, 'Path for migration files. Default: ./scripts/migrations', './scripts/migrations');
        $this->addOption('driver', 'r', Console\Input\InputOption::VALUE_REQUIRED, 'DB driver. Default: mysql', 'mysql');
        $this->addOption('dry-run', 'b', Console\Input\InputOption::VALUE_NONE, 'Pseudo running, no actual change on database, output SQL instead');
	}
	
	protected function outputErrorsAndExit(Console\Output\OutputInterface $output, $code = 1)
	{
		$output->writeln('Errors occurred. See details below.');
		$output->writeln($this->errors);
		$output->writeln('');
		$output->writeln('Usage: ' . $this->getSynopsis());
		exit($code);
	}
	
	protected function outputResult($result, $version, Console\Output\OutputInterface $output)
	{
		if (SchemaManager::RESULT_AT_CURRENT_VERSION == $result){
			$output->writeln('Schema is already at requested version ' . $version);
		} else if (SchemaManager::RESULT_OK == $result){
			$output->writeln('Schema migrated to version ' . $version);
		} else if (SchemaManager::RESULT_NO_MIGRATIONS_FOUND == $result){
			$output->writeln('Unable to find migrations');
		} else {
			throw new \RuntimeException('Unknown migration result');
		}		
	}

	/**
	 * Builds a schema manager from input streams
	 * 
	 * @param \Symfony\Component\Console\Input $input
	 * @param \Symfony\Component\Console\Output $output
	 * @throws \PDOException
	 * @return \Dws\Db\Schema\Manager
	 */
	protected function buildManager(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		$this->populateCommonParams($input, $output);
		$dsn = self::buildDSNForPdo($this->driver, $this->db, $this->host);
		$pdo = new PDO($dsn, $this->user, $this->pass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$manager = new SchemaManager($pdo, $this->path, array(
			'namespace' => $this->namespace,
			'output'	=> $output,
			'dry-run'   => $input->getOption('dry-run'),
		));
		return $manager;
	}
	
	protected static function buildDSNForPdo($driver, $db, $host)
	{
		return $driver . ':dbname=' . $db . ';host=' . $host;
	}

	protected function _getEnvironmentConfig() {
	    if (file_exists(self::$envpath) && is_readable(self::$envpath)) {
    	    $config = json_decode(file_get_contents(self::$envpath));
            return $config->database;
	    }
	}

	protected function populateCommonParams(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		$this->driver = $input->getOption('driver');
		$this->host = trim($input->getOption('host'));
		$this->user = trim($input->getOption('user'));
		$this->pass = trim($input->getOption('pass'));
		$this->db = $input->getOption('db');
		$dsn = $input->getOption('dsn');

		$dbconfig = $this->_getEnvironmentConfig();
		if ($dbconfig) {
			$this->host = $this->host ?: $dbconfig->hostname;
			$this->user = $this->user ?: $dbconfig->username;
			$this->pass = $this->pass ?: $dbconfig->password;
			$this->db   = $this->db   ?: $dbconfig->database;
		}

		if ($dsn){
			$dsnComponents = DsnUtil::parseDSN($dsn);
			$this->driver = $dsnComponents['phptype'];
			$this->host = $dsnComponents['hostspec'];
			$this->user = $dsnComponents['username'];
			$this->pass = $dsnComponents['password'];
			$this->db = $dsnComponents['database'];
		}

		if (!$this->driver){
			$this->errors[] = 'DB driver is required';
		}
		if (!$this->db){
			$this->errors[] = 'DB name is required';
		}
		
		if (count($this->errors) > 0){
			$this->outputErrorsAndExit($output);
		}
		
		$this->namespace = $input->getOption('namespace');
		$this->path = $input->getOption('path');
	}
	
	public function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
	{
		try {
			$this->manager = $this->buildManager($input, $output);
		} catch (\Exception $e) {
			$this->errors[] = $e->getMessage();
			$this->outputErrorsAndExit($output, 1);
		}
	}
	
	protected function displayCurrentSchemaVersion(Console\Output\OutputInterface $output)
	{
		$version = $this->manager->getCurrentSchemaVersion();

		// @see Dws\Sftw\Db\Schema\SchemaManager::_processMigrations() for nice spacing
		$output->writeln(sprintf('Latest schema version: %s', $version));
	}	

	protected function displayRecentSchemaVersions($number, Console\Output\OutputInterface $output)
	{
		$versions = $this->manager->getRecentSchemaVersions($number);

		foreach ($versions as $version) {
		    $output->writeln(sprintf('Recent version: %s    %s    %s', $version->version, $version->deployed_at, $version->migration_filename));
		}
	}	
}

