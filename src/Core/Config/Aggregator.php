<?php namespace Atomino\Core\Config;


class Aggregator implements ProviderInterface {
	/** @var callable[] */
	private array $configsOrProviders;

	public function __construct(
		callable|array ...$configsOrProviders,
	) {
		$this->configsOrProviders = $configsOrProviders;
	}

	public function __invoke(): array {
		return array_replace_recursive(
			...array_map(
			fn($configOrProvider) => is_array($configOrProvider)
				? $configOrProvider
				: $configOrProvider(),
			$this->configsOrProviders
		),
		);
	}
}
