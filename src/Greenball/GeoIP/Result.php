<?php
namespace Greenball\GeoIP;

use ArrayIterator;
use Illuminate\Support\Fluent;

/**
 * @since 0.1.0 the result extends to \Illuminate\Support\Fluent
 */
class Result extends Fluent implements Interfaces\Result {

	/**
	 * Separator character for compact strings.
	 *
	 * @var string
	 */
	const SEPARATOR = '|';

	/**
	 * IP Address.
	 *
	 * @var string
	 */
	protected $ipaddress;

	/**
	 * @since 0.1.0 Set the result's ipaddress.
	 *
	 * @param  string $ipaddress
	 * @return self
	 */
	public function setAddress($ipaddress) {
		$this->ipaddress = $ipaddress;
		return $this;
	}

	/**
	 * @since 0.1.0 Get the result's ipaddress.
	 *
	 * @return string
	 */
	public function getAddress()
	{
		return $this->ipaddress;
	}

	/**
	 * Import attributes from array or string.
	 *
	 * @param  array|string $raw
	 * @return self
	 */
	public function import($raw)
	{
		return is_array($raw) ? $this->importFromArray($raw) : $this->importFromString($raw);
	}

	/**
	 * Split and merge with the schema. Also convert the is* values back to boolean.
	 *
	 * @param  string $raw
	 * @return self
	 */
	public function importFromString($raw)
	{
		$this->attributes = $this->fixTypes(array_combine(array_keys(Locator::getEmptySchema()), explode(self::SEPARATOR, $raw)));
		return $this;
	}

	/**
	 * Sniff out if the array has named keys or need to merge with the schema.
	 *
	 * @param  array $raw
	 * @return self
	 */
	public function importFromArray(array $raw)
	{
		// Load the schema keys for validation.
		$schema 			= array_keys(Locator::getEmptySchema());

		// If the imported array has numeric keys then combine the values.
		$this->attributes = $this->fixTypes(($schema != array_keys($raw)) ? array_combine($schema, $raw) : $raw);

		return $this;
	}

	/**
	 * Change the information's value types to the schema's value types.
	 *
	 * @param  array $attributes
	 * @return array
	 */
	protected function fixTypes($attributes)
	{
		// Load the schema keys for conversion.
		$schema 			= Locator::getEmptySchema();

		foreach ($attributes as $key => &$value) {
			settype($value, gettype($schema[$key]));
		}

		return $attributes;
	}

	/**
	 * Export attributes into an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->attributes;
	}

	/**
	 * Export attributes to compact string format.
	 *
	 * @return boolean
	 */
	public function toString()
	{
		return implode(self::SEPARATOR, array_values(array_map(function($value) {
			return empty($value) ? '' : $value;
		}, $this->attributes)));
	}

	/**
	 * Export attributes to compact string format.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Support for foreach.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->attributes);
	}
}