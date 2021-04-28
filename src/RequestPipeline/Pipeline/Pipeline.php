<?php namespace Atomino\RequestPipeline\Pipeline;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Atomino\dic;

class Pipeline extends Handler implements Nextable {

	/** @var Segment[] */
	private array $queue = [];

	static public function create(callable $builder): static {
		$pipeline = new Pipeline();
		\Closure::bind($builder, $pipeline, get_class($pipeline))($pipeline);
		return $pipeline;
	}

	public function __construct(private Request|null $request = null) { $this->build(); }

	protected function build() { }

	public function pipe($handler, array|null $arguments = null): static {
		$this->push(new Segment($handler, $arguments, $this));
		return $this;
	}

	public function handle(Request|null $request = null): Response|null { return $this->execute($request); }
	public function __invoke(Request|null $request = null): Response|null { return $this->execute($request); }
	public function execute(Request|null $request = null): Response|null {
		$request = $request ?? $this->request ?? dic()->get(Request::class);
		return $this->next($request);
	}

	private function push(Segment $segment) {
		array_push($this->queue, $segment);
	}

	public function next($request): Response|null {
		if (count($this->queue) === 0) return null;
		return array_shift($this->queue)->getHandler()->handle($request);
	}

}
