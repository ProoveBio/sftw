<?php

namespace Dws\Sftw\Db\Schema;

use Symfony\Component\Console;

abstract class AbstractChange
{

	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * @var string
	 */
	protected $tablePrefix;

	/**
	 * @var Console\Output\OutputInterface
	 */
	protected $output;

	/**
	 * @var Boolean
	 */
	protected $dryRun;

	function __construct(\PDO $pdo, $tablePrefix = '', Console\Output\OutputInterface $output = null, $dryRun = false)
	{
		$this->pdo = $pdo;
		$this->tablePrefix = $tablePrefix;
		$this->output = $output;
		$this->dryRun = $dryRun;
	}

	/**
	 * Changes to be applied in this change
	 */
	abstract function up();

	/**
	 * Rollback the changes made in up()
	 */
	abstract function down();
	
	/**
	 * Convenience method for wrapping a query in a try/catch
	 * 
	 * @param string $sql
	 * @throws \RuntimeException
	 */
	protected function querySQL($sql)
	{
	    if ($this->dryRun) {
	        if ($this->output) {
	            $this->output->write('-- ' . str_repeat("-", 77), true, 0);
	            $this->output->write($sql, true, 0);
	            $this->output->write('-- ' . str_repeat("-", 77), true, 0);
	        }
	    } elseif (!$this->pdo->query($sql)) {
			throw new \RuntimeException('Error executing SQL: ' . PHP_EOL . PHP_EOL . $sql);
		}
	}
}

