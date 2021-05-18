<?php

namespace Atomino\Entity;

use Atomino\Core\Application;
use Atomino\Database\Connection;
use Atomino\Entity\Attributes\BelongsTo;
use Atomino\Entity\Attributes\BelongsToMany;
use Atomino\Entity\Attributes\EventHandler;
use Atomino\Entity\Attributes\Field;
use Atomino\Entity\Attributes\HasMany;
use Atomino\Entity\Attributes\Immutable;
use Atomino\Entity\Attributes\Modelify;
use Atomino\Entity\Attributes\Protect;
use Atomino\Entity\Attributes\Relation;
use Atomino\Entity\Attributes\Validator;
use Atomino\Entity\Attributes\Virtual;
use Atomino\Entity\Plugin\Plugin;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\Cache\CacheInterface;

class Model{

	private Connection $connection;
	private string $table;
	private bool $mutable;
	private array $getters = [];
	private array $setters = [];
	private CacheInterface|null $cache = null;
	private Repository $repository;
	private ValidatorSet $validators;
	private array $eventHandlers = [];

	/** @var \Atomino\Entity\Field\Field[] */
	private array $fields = [];
	/** @var Relation[] */
	private array $relations = [];
//	private array $pluginData = [];
	/** @var \Atomino\Entity\Plugin\Plugin[][] */
	private array $plugins = [];
	private \ReflectionClass $entityReflection;

	public function __construct(private string $entity){

		$this->entityReflection = $ENTITY = new \ReflectionClass($entity);

		$this->cache = Application::DIC()->get(Cache::class);
		$this->modelify($ENTITY);
		$this->repository = new Repository($this);

		$this->setVirtuals($ENTITY);
		$this->setRelations($ENTITY);
		$this->setPlugins($ENTITY);
		$this->setValidators($ENTITY);

		$protecteds = $this->getProtecteds($ENTITY);
		$immutables = $this->getImmutables($ENTITY);
		$this->setFields($ENTITY->getParentClass(), $protecteds, $immutables);

		$this->setEventHandlers($ENTITY);
	}

	private function modelify(\ReflectionClass $ENTITY){
		$Modelify = Modelify::get($ENTITY);
		$this->connection = Application::DIC()->get($Modelify->connection);
		$this->table = $Modelify->table;
		$this->mutable = $Modelify->mutable;
	}
	private function getProtecteds(\ReflectionClass $ENTITY): array{
		$protecteds = [];
		$Protects = Protect::all($ENTITY, $ENTITY->getParentClass(), ...$ENTITY->getTraits(), ...$ENTITY->getParentClass()->getTraits());
		foreach ($Protects as $Protect) $protecteds[$Protect->field] = $Protect;
		return $protecteds;
	}
	private function getImmutables(\ReflectionClass $ENTITY): array{
		$immutables = [];
		$Immutables = Immutable::all($ENTITY, $ENTITY->getParentClass(), ...$ENTITY->getTraits(), ...$ENTITY->getParentClass()->getTraits());
		foreach ($Immutables as $Immutable) $immutables[$Immutable->field] = $Immutable;
		return $immutables;
	}
	private function setFields(\ReflectionClass $BASE, array $protecteds, array $immutables){
		foreach (Field::all($BASE) as $FIELD){

			$fieldName = $FIELD->field;

			if (array_key_exists($FIELD->field, $protecteds)){
				$get = $protecteds[$fieldName]->get;
				$set = $protecteds[$fieldName]->set;
			}else $get = $set = null;

			if (array_key_exists($FIELD->field, $immutables)){
				$allowInsert = $immutables[$fieldName]->allowInsert;
				$allowUpdate = false;
			}else $allowInsert = $allowUpdate = true;

			/** @var \Atomino\Entity\Field\Field $field */
			$field = new ( $FIELD->fieldClass )($fieldName, $get, $set, $allowInsert, $allowUpdate, $FIELD->attributes);
			if ($field->isProtected()){
				if (!is_null($getter = $field->getGetter())) $this->getters[$fieldName] = $getter;
				if (!is_null($setter = $field->getSetter())) $this->setters[$fieldName] = $setter;
			}
			$this->fields[$fieldName] = $field;
		}
	}
	private function setVirtuals(\ReflectionClass $ENTITY){
		foreach (Virtual::all($ENTITY) as $Virtual){
			if ($Virtual->get) $this->getters[$Virtual->field] = 'get' . ucfirst($Virtual->field);
			if ($Virtual->set) $this->setters[$Virtual->field] = 'set' . ucfirst($Virtual->field);
		}
	}
	private function setRelations(\ReflectionClass $ENTITY){
		$relations = array_merge(HasMany::all($ENTITY), BelongsTo::all($ENTITY), BelongsToMany::all($ENTITY));
		foreach ($relations as $relation) $this->relations[$relation->target] = $relation;
	}

	private function setPlugins(\ReflectionClass $ENTITY){
		foreach ($ENTITY->getAttributes(Plugin::class, \ReflectionAttribute::IS_INSTANCEOF) as $Plugin){
			if(!array_key_exists($Plugin->getName(), $this->plugins)) $this->plugins[$Plugin->getName()] = [];
			$this->plugins[$Plugin->getName()][] = $Plugin->newInstance();
		}
	}

	private function setValidators(\ReflectionClass $ENTITY){
		$this->validators = new ValidatorSet();
		$Validators = Validator::all($ENTITY, $ENTITY->getParentClass());
		foreach ($Validators as $Validator){
			$this->validators->addValidator($Validator->field, $Validator->validator);
		}
	}
	private function setEventHandlers(\ReflectionClass $ENTITY){
		$methods = $ENTITY->getMethods();
		foreach ($methods as $method){
			if ($attr = EventHandler::get($method)){
				foreach ($attr->events as $event){
					if (!array_key_exists($event, $this->eventHandlers)) $this->eventHandlers[$event] = [];
					$this->eventHandlers[$event][] = $method->getName();
				}
			}
		}
	}

	/** @return \Atomino\Entity\Field\Field[] */
	public function isMutable(): bool{ return $this->mutable; }
	public function getTable(): string{ return $this->table; }
	public function getConnection(): Connection{ return $this->connection; }
	public function getEntity(): string{ return $this->entity; }
	public function getRepository(): Repository{ return $this->repository; }

	public function getFields(): array{ return $this->fields; }
	#[Pure] public function getField(string $name): \Atomino\Entity\Field\Field|null{ return array_key_exists($name, $this->fields) ? $this->fields[$name] : null; }
	#[Pure] public function hasField(string $name): bool{ return array_key_exists($name, $this->fields); }

	public function getCache(): CacheInterface{ return $this->cache; }
	#[Pure] public function generateCacheKey(int $id): string{ return md5($this->entity . '.' . $id); }

	#[Pure] public function hasGetter($name): bool{ return array_key_exists($name, $this->getters); }
	public function getGetter($name): string{ return $this->getters[$name]; }
	#[Pure] public function hasSetter($name): bool{ return array_key_exists($name, $this->setters); }
	public function getSetter($name): string{ return $this->setters[$name]; }

	#[Pure] public function hasRelation($name): bool{ return array_key_exists($name, $this->relations); }
	public function getRelation($name): Relation{ return $this->relations[$name]; }
	/** @return \Atomino\Entity\Attributes\Relation[] */
	public function getRelations(): array{ return $this->relations; }

	public function getValidators(): ValidatorSet{ return $this->validators; }

	/**
	 * @param $event
	 * @return string[]
	 */
	#[Pure] public function getEventHandlers($event): array{ return array_key_exists($event, $this->eventHandlers) ? $this->eventHandlers[$event] : []; }
	//public function getPluginData(string $name): array{ return array_key_exists($name, $this->pluginData) ? $this->pluginData[$name] : []; }
	/**
	 * @param string $name
	 * @return Plugin[]
	 */
	#[Pure] public function getPlugin(string $name):array{ return array_key_exists($name, $this->plugins) ? $this->plugins[$name] : []; }

	public function getEntityReflection(): \ReflectionClass{ return $this->entityReflection; }

}