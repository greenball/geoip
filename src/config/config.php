<?php
return array(
	/**
	 * Fallback IP address for CLI requests.
	 *
	 * @var string
	 */
	'fallback'	=> '127.0.0.1',

	/**
	 * Allow / deny reserved & private addresses.
	 * This value will be directly passed to the filter_var function.
	 * Set it to null to remove restructions.
	 *
	 * @link http://php.net/manual/en/filter.filters.validate.php
	 * @link http://en.wikipedia.org/wiki/Reserved_IP_addresses#Reserved_IPv4_addresses
	 * @var integer
	 */
	'reserved'	=> FILTER_FLAG_NO_RES_RANGE,

	/**
	 * Driver used to fetch the location.
	 * @since 0.1.0 the default driver is SmartIP
	 *
	 * @var string
	 */
	'driver'	=> 'SmartIP',

	/**
	 * List of supported drivers and their configurations.
	 *
	 * @var array
	 */
	'drivers'	=> array(
		'FreeGeoIP'	=> null,
		'Telize'	=> null,
		'SmartIP'	=> null,
	),

	/**
	 * Result cache settings.
	 *
	 * @var array
	 */
	'cache'		=> array(
		/**
		 * Cacheing interval for results in minutes.
		 * Ips are updated very frequently now days,
		 * so you should not set a too long interval if you
		 * lack on memory or space where to cache the results.
		 *
		 * @var integer
		 */
		'interval'	=> 10080, // 7 days

		/**
		 * Prefix used in the cache since the script
		 * generates they keys by making an md5 hash
		 * of the user agent, with this can be sure
		 * to not to conflict with other entries.
		 *
		 * @var string
		 */
		'prefix'	=> 'gi_',
	),
);