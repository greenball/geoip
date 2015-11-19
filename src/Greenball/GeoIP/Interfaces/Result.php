<?php
namespace Greenball\GeoIP\Interfaces;

use IteratorAggregate;

interface Result extends IteratorAggregate {

	/**
	 * Import informations from array or string.
	 *
	 * @param  array|string $raw
	 * @return self
	 */
	public function import($raw);

	/**
	 * Split and merge with the schema. Also convert the is* values back to boolean.
	 *
	 * @param  string $raw
	 * @return self
	 */
	public function importFromString($raw);

	/**
	 * Sniff out if the array has named keys or need to merge with the schema.
	 *
	 * @param  array $raw
	 * @return self
	 */
	public function importFromArray(array $raw);

	/**
	 * Export informations to compact string format.
	 *
	 * @return boolean
	 */
	public function toString();

	/**
	 * Export informations into an array.
	 *
	 * @return array
	 */
	public function toArray();
	
}