<?php

namespace Ceceply\Action\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ActionMakeCommand extends InterfaceActionMakeCommand
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:action
							{name : Action class name}
							{--i|interface= : Custom interface name}
							{--w|without-interface : Make action class without implementing an action interface}
							{--a|action= : Custom action name}
							{--m|model= : The model to be processed}
							{--g|guess-model : Guess model by the last action class name word}
							{--f|force : Create the class even if the class already exists}
							{--F|force-both : Create the interface and the class even if the interface already exists}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new action class';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub(): string
	{
		return __DIR__ . '/../../../stubs/action.stub';
	}

	/**
	 * Execute the console command.
	 *
	 * @return bool|null
	 *
	 * @throws FileNotFoundException
	 */
	public function handle(): bool|null
	{
		if ($this->withInterface()) {
			$this->call(InterfaceActionMakeCommand::class, [
				'name' => $this->getInterfaceName(),
				'--action' => $this->getAction(),
				'--model' => $this->getModelInput(),
				'--guess-model' => $this->guessModel(),
				'--force' => $this->forceBoth(),
			]);
		}

		if (!$this->option('force') && $this->forceBoth()) {
			$this->input->setOption('force', true);
		}

		return parent::handle();
	}

	/**
	 * Build the class with the given name.
	 *
	 * @param string $name
	 * @return string
	 *
	 * @throws FileNotFoundException
	 */
	protected function buildClass($name): string
	{
		$stub = parent::buildClass($name);

		return $this->replaceInterface($stub, $this->getInterfaceReplace());
	}

	/**
	 * Get a list of imports to add to the stub.
	 *
	 * @return array
	 */
	protected function getImports(): array
	{
		$imports = parent::getImports();

		if ($this->withInterface()) {
			$interface = $this->qualifyClass($this->getInterfaceName());

			$imports[] = "use $interface;";
		}

		return $imports;
	}

	/**
	 * Replace the interface for the given stub.
	 *
	 * @param string $stub
	 * @param string $interface
	 * @return string
	 */
	protected function replaceInterface(string $stub, string $interface): string
	{
		return str_replace(['DummyInterface', '{{ interface }}', '{{interface}}'], $interface, $stub);
	}

	/**
	 * Get interface name for replace interface stub placeholder.
	 *
	 * @return string
	 */
	protected function getInterfaceReplace(): string
	{
		$interface = last(explode('\\', $this->getInterfaceName()));

		return $this->withInterface() ? 'implements ' . $interface : '';
	}

	/**
	 * Get interface name from input or return default interface.
	 *
	 * @return string
	 */
	protected function getInterfaceName(): string
	{
		if ($this->option('interface')) {
			return trim($this->option('interface'));
		}

		return $this->getDefaultInterface($this->getNameInput());
	}

	/**
	 * Make default interface name from class name.
	 *
	 * @param string $class
	 * @return string
	 */
	protected function getDefaultInterface(string $class): string
	{
		$a = explode('/', $class);
		$a[count($a) - 1] = 'Contracts\\' . implode('', Arr::map(Str::ucsplit(last($a)), fn($section) => Str::plural($section)));

		return implode('\\', $a);
	}

	/**
	 * Confirm to create action class without implementing interface.
	 *
	 * @return bool
	 */
	protected function withInterface(): bool
	{
		return ! $this->option('without-interface');
	}

	/**
	 * Confirm to force create interface and class.
	 *
	 * @return bool
	 */
	protected function forceBoth(): bool
	{
		return $this->option('force-both');
	}
}
