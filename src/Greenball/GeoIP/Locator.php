<?php
namespace Greenball\GeoIP;

// Illuminate framework.
use Illuminate\Support\Manager;
use Illuminate\Foundation\Application;

class Locator extends Manager {

	/**
	 * Runtime cache for results.
	 *
	 * @var array
	 */
	protected $registry 	= array();

	/**
	 * Store the app.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Store the config.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Defines a standard data schema, every driver supposed to convert
	 * the fetched data onto this schema.
	 *
	 * @var array
	 */
	protected static $schema = array(
		'countryCode'		=> '', // string
		'countryName'		=> '', // string
		'regionName'		=> '', // string
		'city'				=> '', // string
		'zipcode'			=> '', // string
		'latitude'			=> 0.0, // floating integer
		'longitude'			=> 0.0, // floating integer
		'areaCode'			=> '', // string
	);

	/**
	 * Create a new locator instance.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @param  array $config
	 * @return void
	 */
	public function __construct($app, $config)
	{
		$this->app 		= $app;
		$this->config 	= $config;
	}

	/**
	 * Get an empty information schema array.
	 *
	 * @return array
	 */
	public static function getEmptySchema()
	{
		return static::$schema;
	}

	/**
	 * Reflect calls to the result object.
	 *
	 * @throws \Greenball\GeoIP\Exceptions\InvalidCallException if the called method
	 * do not exists in the attributes array, or not a method of the result object.
	 *
	 * @param  string $method
	 * @param  array $params
	 * @return mixed
	 */
	public function __call($method, $params)
	{
		// When calling GeoIP::importFromString() etc..
		// then direcly provide a new result object.
		if (substr($method, 0, 6) == 'import') {
			return call_user_func_array(array($this->getEmptyResult(), $method), $params);
		}

		$reflection 	= $this->locate(null);

		// Reflect an information.
		if ($reflection->offsetExists($method)) {
			return $reflection->offsetGet($method);
		}

		// Reflect a method.
		if (method_exists($reflection, $method)) {
			return call_user_func_array(array($reflection, $method), $params);
		}

		throw new Exceptions\InvalidCallException($method . 'does not exists on the '.get_class($reflection).' object.');
	}

	/**
	 * @since 0.1.0 Create an empty result object.
	 *
	 * @return mixed
	 */
	public function getEmptyResult()
	{
		return $this->app->make('geoip.result');
	}

	/**
	 * Try to guess the visitor's current ip address.
	 * We cannot be sure the visitor is not behind a proxy.
	 * @see http://en.wikipedia.org/wiki/X-Forwarded-For
	 *
	 * @return string IPAddress
	 */
	public function visitorsAddress()
	{
		// Check the visitor may use a proxy.
		if (( $forwarded = $this->app['request']->server('HTTP_X_FORWARDED_FOR')) != false) {
			if (preg_match('%(?<client>[\d]+\.[\d]+\.[\d]+\.[\d]+)%', $forwarded, $match)) {
				$ipaddress 	= $match['client'];
			}
		} 
		// CLI request do not sending IP address so to avoid the further conflict we set a fallback.
		else {	
			$ipaddress 	= $this->app['request']->server('HTTP_REMOTE_ADDR', $this->config['fallback']);
		}

		return $ipaddress;
	}

	/**
	 * Detect the location from an IPV4 address.
	 * If no IP address provided the class will use the current
	 * visitor's ipaddress.
	 * @throws \Greenball\GeoIP\Exceptions\InvalidAddressException when an invalid address being located.
	 *
	 * @param  string|null $ipaddress
	 * @return mixed Result of the fetch.
	 */
	public function locate($ipaddress = null)
	{
		// Check for visitors address if null was given.
		$ipaddress 		= $ipaddress ?: $this->visitorsAddress();

		// Check in the runtime cache.
		if (array_key_exists($ipaddress, $this->registry)) {
			return $this->registry[$ipaddress];
		}

		// Validate the IP address.
		if ( ! $this->validate($ipaddress)) {
			throw new Exceptions\InvalidAddressException($ipaddress);
		}

		// Get cache settings.
		$key 		= $this->config['cache']['prefix'].ip2long($ipaddress);
		$interval 	= $this->config['cache']['interval'];

		// Compability for PHP 5.3
		$self 		= $this;

		// Fetch from the cache, we keep the results
		// in compact string format to minimalize the
		// cache useage.
		$result 	= $this->app['cache']->remember($key, $interval, function() use ($self, $ipaddress) {
			return $self->_fetch($ipaddress)->toString();
		});

		return $this->registry[$ipaddress] = $this->getEmptyResult()->importFromString($result)->setAddress($ipaddress);
	}

	/**
	 * Fetch the location and return with the result object.
	 *
	 * @param  string $ipaddress
	 * @return mixed
	 */
	public function _fetch($ipaddress)
	{
		// Get the driver.
		$driver 	= $this->driver();

		// Convert it to the schema.
		$schema 	= array_merge($this->getEmptySchema(), $driver->convert($driver->fetch($ipaddress)));
		
		return $this->getEmptyResult()->importFromArray($schema);
	}

	/**
	 * Validate an IPV4 format.
	 *
	 * @param  string $ipaddress
	 * @return boolean
	 */
	public function validate($ipaddress)
	{
		return filter_var($ipaddress, FILTER_VALIDATE_IP, $this->app['config']->get('geoip::reserved'));
	}

	/**
	 * Create FreeGeoIP driver.
	 * @since 0.0.1
	 *
	 * @return \Greenball\GeoIP\Drivers\FreeGeoIP
	 */
	public function createFreeGeoIPDriver()
	{
		return new Drivers\FreeGeoIP($this->app->config['geoip::drivers.FreeGeoIP']);
	}

	/**
	 * Create Telize driver.
	 * @since 0.1.0
	 *
	 * @return \Greenball\GeoIP\Drivers\Telize
	 */
	public function createTelizeDriver()
	{
		return new Drivers\Telize($this->app->config['geoip::drivers.Telize']);
	}

	/**
	 * Create Telize driver.
	 * @since 0.1.0
	 *
	 * @return \Greenball\GeoIP\Drivers\Telize
	 */
	public function createSmartIPDriver()
	{
		return new Drivers\SmartIP($this->app->config['geoip::drivers.SmartIP']);
	}

	/**
	 * Get the default driver name.
	 *
	 * @return string
	 */
	protected function getDefaultDriver()
	{
		return $this->app['config']->get('geoip::driver');
	}
}