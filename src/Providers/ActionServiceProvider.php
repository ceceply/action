<?php

namespace Ceceply\Action\Providers;

use Ceceply\Action\Console\Commands\ActionMakeCommand;
use Ceceply\Action\Console\Commands\InterfaceActionMakeCommand;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$this->commands([
			InterfaceActionMakeCommand::class,
			ActionMakeCommand::class,
		]);
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		//
	}
}
