<?php
namespace Greenball\GeoIP\Drivers;

abstract class BaseDriver {

	/**
	 * Store the driver configuration.
	 *
	 * @var mixed
	 */
	protected $config;

	/**
	 * Init the driver configuration.
	 *
	 * @return void
	 */
	public function __construct($config = null)
	{
		$this->config = $config;
	}

}