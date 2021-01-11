<?php namespace Atomino\Database;

use Atomino\Database\Finder\Filter;
use Symfony\Component\Cache\CacheItem;

class Finder{

	private int $cacheInterval = 0;
	private array $select = [];
	private ?Filter $filter = null;
	private string $from;
	private array $order = [];

	public function __construct(private Connection $connection){ }
	public function cache(int $sec): static{
		$this->cacheInterval = $sec;
		return $this;
	}
	public function fields(string ...$select): static{
		$select = array_map(function ($field): string{ return $this->connection->escape($field); }, $select);
		return $this->select(join(',', $select));
	}
	public function select(string|null $select = null): static{
		if(is_null($select)) $this->select = [];
		else $this->select[] = $select;
		return $this;
	}

	public function table(string $from): static{
		return $this->from($this->connection->escape($from));
	}

	public function from(string $from): static{
		$this->from = $from;
		return $this;
	}
	public function where(Filter $filter): static{
		if (is_null($this->filter)){
			$this->filter = $filter;
		}else{
			$this->filter->and($filter);
		}
		return $this;
	}

	public function asc(?string $field): static{
		$this->order[] = $this->connection->escape($field) . ' ASC';
		return $this;
	}
	public function desc(?string $field): static{
		$this->order[] = $this->connection->escape($field) . ' DESC';
		return $this;
	}

	public function field(): mixed{ return is_null($record = $this->record()) ? null : reset($record); }
	public function integer(): int|null{ return is_null($record = $this->record()) ? null : intval(reset($record)); }

	public function record(): ?array{
		$items = $this->records(1);
		return array_pop($items);
	}

	public function records(?int $limit = null, ?int $offset = null, null|bool &$count = false): array{
		$sql = $this->buildSQL($limit, $offset, $count);

		if ($this->connection->getCache() && $this->cacheInterval){
			$cached = $this->connection->getCache()->get(md5($sql), function (CacheItem $item) use ($sql, $count){
				$records = $this->connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
				if ($count !== false) $count = $this->connection->query('SELECT FOUND_ROWS()')->fetch(\PDO::FETCH_COLUMN);
				$item->expiresAfter($this->cacheInterval);
				return ['records' => $records, 'count' => $count];
			});
			$records = $cached['records'];
			$count = $cached['count'];
		}else{
			$records = $this->connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
			if ($count !== false) $count = $this->connection->query('SELECT FOUND_ROWS()')->fetch(\PDO::FETCH_COLUMN);
		}

		return $records;
	}

	protected function buildSQL(?int $limit, ?int $offset, null|bool $count): string{
		return
			'SELECT ' .
			( $count !== false ? 'SQL_CALC_FOUND_ROWS ' : '' ) .
			( count($this->select) ? join(',', $this->select) : '*' ) . ' ' .
			' FROM ' . $this->from . ' ' .
			( $this->filter != null && !is_null($filter = $this->filter->getSql($this->connection)) ? ' WHERE ' . $filter . ' ' : '' ) .
			( count($this->order) ? ' ORDER BY ' . join(', ', $this->order) : '' ) .
			( $limit ? ' LIMIT ' . $limit : '' ) .
			( $offset ? ' OFFSET ' . $offset : '' );
	}

}