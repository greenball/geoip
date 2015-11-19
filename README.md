### Greenball GeoIP locator for Laravel 4.
***
This package is able to locate an IPV4 Address geo position. The locating are driver based and the locator ships with multiple free API support such as **freegeoip.net**, **telize.com**, **smart-ip.net** and counting... With those APIs result builds an standard data schema and provides an easly managable information layer.

### Features
***

+ Driver based locating.
+ Locate address' country.
+ Locate address' city.
+ Locate address' latitude.
+ Locate address' longitude.
+ Look behind proxies.
+ Easy costumization by a simple config file.
+ **Import** & **Export** results into compact string.
+ Out of the box **caching**!

### Installation
***

First add the package to your composer:
```json
{
    "require": {
        "greenball/geoip": "1.*"
    }
}
```
After the composer update/install add the service provider to your app.php:
```php
'providers' => array(
    // ...
    'Greenball\GeoIP\Providers\GeoIPServiceProvider',
    // ...
)
```
Add the alias to the aliases in your app.php:
```php
'aliases' => array(
    // ...
    'GeoIP' => 'Greenball\GeoIP\Facades\GeoIP',
)
```
You can use personal configurations just publish the package's configuration files.
```php
php artisan config:publish greenball/geoip
```
Finaly, enjoy :3

### Drivers
***
The package ships with multiple free geo api driver. You just need to choose which service you prefer in the config file. Currently supported drivers:

+ FreeGeoIP by [freegoip.net](http://freegoip.net/)
+ SmartIP by [smart-ip.net](http://smart-ip.net/)
+ Telize by [telize.com](http://telize.com/)

*If you wana found any free api and wana see it in the package do a PR or make an Issue*

### Usage
***
```php
// Fetch the current visitor's geo location.
$result 	= GeoIP::locate();

// Every locating will product a standard data schema.
Greenball\GeoIP\Result Object
(
    [ipaddress:protected] => 216.239.51.99
    [attributes:protected] => Array
        (
            [countryCode] => US
            [countryName] => United States
            [regionName] => California
            [city] => Mountain View
            [zipcode] => 
            [latitude] => 37.4192
            [longitude] => -122.057
            [areaCode] => 
        )

)

// Locate a stored IP address.
GeoIP::locate('86.89.95.124');

// Import results from database varchar field.
GeoIP::importFromString('US|United States|California|Mountain View||37.4192|-122.057|');

// Export results to compact string.
$string = (string) GeoIP::locate();
$string = GeoIP::toString();
echo GeoIP::locate();

// The result also implements the ArrayIterator so can use like this.
foreach(GeoIP::locate('178.88.16.44') as $key => $value) {
    echo 'Your '.$key.' is '.var_export($value, true).'.<br>';
}
```

### Examples
***
The Locator facade useing magic calls for easier codeing, every function request will be mirrored to the result object.

```php
// Get the visitors city name.
echo 'You visiting from '.GeoIP::city();

// Get geo position.
echo 'Your latitude is '.GeoIP::latitude().' and longitude is '.GeoIP::longitude();
```

You can use the countryCode to deliver the content in your visitor's language.
```php
// Ofc, its good idea to verify you support the language first but the mechanism is like this :3
App::setLocale(strtolower(GeoIP::countryCode()));
```

### Import & Export results
***
You can export & import the result object informations into a simple array or a compact string. This function useful when you wish to store a result in a database field, the compact string format will only contain the result values so it can be between 30-200 chr which fits perfectly in your database char field.

```php
// Export to compact string.
GeoIP::detect()->toString(); // Will produce US|United States|California|Mountain View||37.4192|-122.057|

// Import from compact string.
GeoIP::importFromString('PL|Poland|Lodzkie|Lodz||51.75|19.4667|');

// Export result to an array.
$infoArray = GeoIP::toArray(); // Will produce a simple array with the result object data values.

// Import result from an array.
// if you pass a numeric keyed array to the function that will 
// sniff it out and combine the schema keys and the imported data values to the object.
GeoIP::importFromArray($infoArray); // Will revert every informations.

// Also there is a base function which can sniff the
// passed argument's type an call the right function for it.
GeoIP::import('Can be string or an array here.');
```

### Results
***
The package uses the Laravel framework's awesome IoC with this you can inject your own solutions.

```php
// Make the locator from it's container.
App::make('geoip')->locate();

// Make an empty result object from the container.
App::make('geoip.result')
    ->import('NO|Norway|Oslo|Christiania||59.9167|10.75|');

// Inject your own result object.
App::bind('geoip.result', 'My\Custom\Result');
```

### Cacheing
***
The package useing your running app's cache so you don't have to set up anything ;) Also it uses a really really small footprint with the compact string solution.

Cacheing in theory: the results are converted into an unindexed array in the standard schema's order, then remove the empty values like 0, false, null to save the unecessary zeros and that string will be imported & exported from / to cache. When the script imports a result from a string the result object uses the standard schema and converts the value types back.

### Config
***

+ You can set a fallback IPV4 address for CLI request to not to cause conflicts.
+ Filter reserved and private addresses.
+ Driver which used on locating.
+ Driver configs, if the driver require secret key or any config.
+ Cache interval and key prefix.

### Changes
***

+ 0.1.0 Result object become an instance of Illuminate\Support\Fluent for less codeing, fixed a locator bug on reflections, added and set the SmartIP as default driver.