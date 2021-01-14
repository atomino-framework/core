<?php namespace Atomino\Database;

use JetBrains\PhpStorm\Pure;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class Connection{

	private \PDO $pdo;

	private ?Smart $smart = null;

	public function __construct(private string $dsn, private ?CacheInterface $cache, private LoggerInterface $logger){
		$this->pdo = new \PDO($this->dsn);
	}

	public function query(string $query): bool|\PDOStatement{
		$this->logger->info($query);
		try{
			return $this->pdo->query($query);
		}catch (\Exception $exception){
			$this->logger->error($exception->getMessage(), [$query]);
			throw $exception;
		}
	}
	public function quote(mixed $subject, bool $qm = true): string{ return $subject === null ? 'NULL' : ( $qm ? $this->pdo->quote($subject) : trim($this->pdo->quote($subject), "'") ); }
	public function escape($subject): string{ return '`' . $subject . '`'; }

	public function getPdo(): \PDO{ return $this->pdo; }
	public function getDsn(): string{ return $this->dsn; }
	public function getCache(): ?CacheInterface{ return $this->cache; }

	#[Pure] public function getFinder(): Finder{ return new Finder($this); }
	public function getDescriptor(): Descriptor{ return new Descriptor($this); }
	public function getDumper(string $path, string $tmp): Dumper{ return new Dumper($this, $path, $tmp); }
	public function getMigrator($path, $table): Migrator{ return new Migrator($this, $path, $table); }
	public function getSmart(): Smart{ return $this->smart ? $this->smart : $this->smart = new Smart($this); }

}