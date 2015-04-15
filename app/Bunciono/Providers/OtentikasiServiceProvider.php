<?php namespace App\Bunciono\Providers;

use Illuminate\Support\ServiceProvider;

class OtentikasiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	//protected $defer = true;


	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('otentikasi', function()
		{
			return new \App\Bunciono\Libraries\Keamanan;
		});

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	/*public function provides()
	{
		return ['Otentikasi'];
	}*/

}
