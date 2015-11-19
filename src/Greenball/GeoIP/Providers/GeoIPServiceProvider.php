<?php 
namespace Greenball\GeoIP\Providers;

use Greenball\GeoIP\Locator;
use Illuminate\Support\ServiceProvider;

class GeoIPServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
		$this->package('greenball/geoip', 'geoip', realpath(__DIR__.'/../../../'));
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerLocator();
		$this->registerResult();
	}

	/**
	 * Register the localizator.
	 * @since 0.1.0 the locator requires the config as the second param.
	 *
	 * @return void
	 */
	public function registerLocator()
	{
		$this->app['geoip'] = $this->app->share(function($app) {
			return new Locator($app, $app['config']->get('geoip::config'));
		});
	}

	/**
	 * Register the result handler.
	 *
	 * @return void
	 */
	public function registerResult()
	{
		$this->app->bind('geoip.result', 'Greenball\GeoIP\Result');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('geoip', 'geoip.result');
	}
}