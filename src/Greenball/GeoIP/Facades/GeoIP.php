<?php 
namespace Greenball\GeoIP\Facades;

use Illuminate\Support\Facades\Facade;

class GeoIP extends Facade {
	
    /**
     * Get the registered component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'geoip'; }
}