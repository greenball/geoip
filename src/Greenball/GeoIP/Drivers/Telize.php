<?php
namespace Greenball\GeoIP\Drivers;

use Greenball\GeoIP\Interfaces\Driver;

/**
 * @link http://www.telize.com/
 */
class Telize extends BaseDriver implements Driver {

	/**
	 * Api's base url.
	 *
	 * @var string
	 */
	const BASE_URL = 'http://www.telize.com/geoip';

	/**
	 * Fetch the result.
	 *
	 * @param  string $ipaddress
	 * @return array
	 */
	public function fetch($ipaddress) {
		// Create request url.
		$url 	= self::BASE_URL.'/'.$ipaddress;

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
			'country'		=> 'countryName',
			'region'		=> 'regionName',
			'city'			=> 'city',
			'postal_code '	=> 'zipcode',
			'latitude'		=> 'latitude',
			'longitude'		=> 'longitude',
			'area_code '	=> 'areaCode',
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