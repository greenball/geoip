<?php
namespace Greenball\GeoIP\Drivers;

use Greenball\GeoIP\Interfaces\Driver;

/**
 * @link http://freegeoip.net/
 */
class FreeGeoIP implements Driver {

	/**
	 * Api's base url.
	 *
	 * @var string
	 */
	const BASE_URL = 'http://freegeoip.net';

	/**
	 * Fetch the result.
	 *
	 * @param  string $ipaddress
	 * @return array
	 */
	public function fetch($ipaddress) {
		// Create request url.
		$url 	= self::BASE_URL.'/json/'.$ipaddress;

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
			'country_code'	=> 'countryCode',
			'country_name'	=> 'countryName',
			'region_name'	=> 'regionName',
			'city'			=> 'city',
			'zipcode'		=> 'zipcode',
			'latitude'		=> 'latitude',
			'longitude'		=> 'longitude',
			'areacode'		=> 'areaCode',
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