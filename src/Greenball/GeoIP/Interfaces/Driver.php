<?php
namespace Greenball\GeoIP\Interfaces;

interface Driver {

	/**
	 * Fetch the result.
	 *
	 * @param  string $ipaddress
	 * @return mixed
	 */
	public function fetch($ipaddress);

	/**
	 * Convert the result into the standard format.
	 *
	 * @return array
	 */
	public function convert($result);
}