<?php
namespace Greenball\GeoIP\Drivers;

use Greenball\GeoIP\Interfaces\Driver;

/**
 * @link http://smart-ip.net/geoip-api
 * @since 0.1.0
 */
class SmartIP implements Driver {

	/**
	 * Api's base url.
	 *
	 * @var string
	 */
	const BASE_URL = 'http://smart-ip.net';

	/**
	 * Fetch the result.
	 *
	 * @param  string $ipaddress
	 * @return array
	 */
	public function fetch($ipaddress) {
		// Create request url.
		$url 	= self::BASE_URL.'/geoip-json/'.$ipaddress.'/a';

		return json_decode(file_get_contents($url), true);
	}

	/**
	 * Convert the result into the standard format.
	 *
	 * @return array
	 */
	public function convert($result) {
		// Key conversion table.
		$conversion 	= array(
			'countryCode'	=> 'countryCode',
			'countryName'	=> 'countryName',
			'region'		=> 'regionName',
			'city'			=> 'city',
			'latitude'		=> 'latitude',
			'longitude'		=> 'longitude',
		);

		$converted  = array();

		foreach ($result as $key => $value) {
			if (array_key_exists($key, $conversion)) {
				$converted[$conversion[$key]] = $value;
			}
		}

		return $converted;
	}

}