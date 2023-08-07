<?php

namespace Ceceply\Action\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;

class InterfaceActionMakeCommand extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:iaction
							{name : The interface name of the action}
							{--a|action= : The action name}
							{--m|model= : The model to be processed}
							{--f|force : Create the interface even if the interface already exists}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new action interface';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Action';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub(): string
	{
		return __DIR__ . '/stubs/action.interface.stub';
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param string $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace): string
	{
		return "$rootNamespace\\Actions";
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

		return $this
			->replaceImports($stub, $this->getImports())
			->replaceAction($stub, $this->getAction())
			->replaceParameters($stub, $this->getActionParameters());
	}

	/**
	 * Replace the imports for the given stub.
	 *
	 * @param string $stub
	 * @param array $imports
	 * @return $this
	 */
	protected function replaceImports(string &$stub, array $imports): static
	{
		$stub = str_replace(
			['DummyImport', 'DummyImports', '{{ import }}', '{{ imports }}', '{{import}}', '{{imports}}'],
			(empty($imports) ? '' : "\n" . implode("\n", $imports) . "\n"),
			$stub
		);

		return $this;
	}

	/**
	 * Get a list of imports to add to the stub.
	 *
	 * @return array
	 */
	protected function getImports(): array
	{
		$imports = [];

		if ($model = $this->getModelInput()) {
			$imports[] = 'use ' . $this->qualifyModel($model) . ';';
		}

		return $imports;
	}

	/**
	 * Replace the action for the given stub.
	 *
	 * @param string $stub
	 * @param string $name
	 * @return $this
	 */
	protected function replaceAction(string &$stub, string $name): static
	{
		$action = (string) str($name)->lower()->singular();

		$stub = str_replace(['DummyAction', '{{ action }}', '{{action}}'], $action, $stub);

		return $this;
	}

	/**
	 * Get action name from input or use default name.
	 *
	 * @return string
	 */
	protected function getAction(): string
	{
		return $this->getActionInput() ?: $this->getDefaultAction($this->getNameInput());
	}

	/**
	 * Get default action name from class name.
	 *
	 * @param string $name
	 * @return string
	 */
	protected function getDefaultAction(string $name): string
	{
		$name = last(explode('/', $name));

		return str(Str::ucsplit($name)[0])->lower()->singular();
	}

	/**
	 * Replace the action parameters for the given stub.
	 *
	 * @param string $stub
	 * @param array $parameters
	 * @return string
	 */
	protected function replaceParameters(string $stub, array $parameters): string
	{
		return str_replace(
			['DummyParameters', 'DummyParameter', '{{ parameters }}', '{{ parameter }}', '{{parameters}}', '{{parameter}}'],
			implode(', ', $parameters),
			$stub
		);
	}

	/**
	 * Get action parameters.
	 *
	 * @return array
	 */
	protected function getActionParameters(): array
	{
		$params = [];

		if ($model = $this->getModelInput()) {
			$model = last(explode('\\', $model));
			$params[] = $model . ' $' . strtolower($model);
		}

		if (Str::is(['create', 'update'], $this->getAction())) {
			$params[] = 'array $inputs';
		}

		return $params;
	}

	/**
	 * Get the desired action name from the input.
	 *
	 * @return string
	 */
	protected function getActionInput(): string
	{
		return trim($this->option('action'));
	}

	/**
	 * Get the desired model name from the input.
	 *
	 * @return string
	 */
	protected function getModelInput(): string
	{
		return trim($this->option('model'));
	}
}
