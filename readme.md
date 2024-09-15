# WordPress Plugin

This library is WordPress plugin singleton base class with methods to get data from [plugin's header fields](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/). 

## Installation

```console
composer require piotrpress/wordpress-plugin
```

## Usage

```php
/**
 * Plugin Name:       Example Plugin
 * Plugin URI:        https://example.com/plugin/
 * Description:       Example Plugin description.
 * Version:           1.0.0
 * Requires at least: 6.2.2
 * Requires PHP:      7.4
 * Author:            John Smith
 * Author URI:        https://example.com/plugin/author/
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Update URI:        https://example.com/plugin/update/
 * Text Domain:       example-plugin
 * Domain Path:       /languages
 */

require __DIR__ . '/vendor/autoload.php';

use PiotrPress\WordPress\Plugin;

class Example extends Plugin {
    public function activation() : void {}
    public function deactivation() : void {}
}

Example::getInstance( __FILE__ );

echo Example::getName();
```

**NOTE:** Plugin's translations are loaded automatically according to `Text Domain` and `Domain Path` plugin's header fields. 

## Methods

### Basic static methods handling plugin's default header fields

* `getName()` - returns `string` with the name of the plugin from `Plugin Name` header field
* `getPluginURI()` - returns `string` with the home page of the plugin or empty `string` if `Plugin URI` header field is not set
* `getVersion()` - returns `string` with the current version number of the plugin or empty `string` if `Version` header field is not set
* `getDescription()` - returns `string` with a short description of the plugin or empty `string` if `Description` header field is not set
* `getAuthor()` - returns `string` with the name of the plugin author or empty `string` if `Author` header field is not set
* `getAuthorURI()` - returns `string` with the website of the plugin's author or empty `string` if `Author URI` header field is not set
* `getTextDomain()` - returns `string` with the gettext text domain of the plugin or directory name of the plugin if `Text Domain` header field is not set
* `getDomainPath()` - returns `string` with the path to translations directory or empty `string` if `Domain Path` header field is not set
* `getNetwork()` - returns `bool` whether the plugin can only be activated network-wide according to `Network` header field
* `getRequiresWP()` - returns `string` with the lowest WordPress version that the plugin will work on or empty `string` if `Requires at least` header field is not set
* `getRequiresPHP()` - returns `string` with the minimum required PHP version or empty `string` if `Requires PHP` header field is not set
* `getUpdateURI()` - returns `string` with third-party plugin's update server or empty `string` if `Update URI` header field is not set
* `RequiresPlugins()` - returns `string` with the comma-separated list of WordPress.org-formatted slugs for its dependencies or empty `string` if `Requires Plugins` header field is not set

### Additional static methods handling plugin's paths

* `getSlug()` - returns `string` with the sanitized name of the plugin
* `getPrefix()` - returns `string` with the prefix for plugin's hooks
* `getFile()` - returns `string` with the path to main plugin's file
* `getDir()` - returns `string` with the path to plugin's directory
* `getUrl()` - returns `string` with the url to plugin's directory
* `getBaseName()` - returns `string` with the basename of the plugin
* `getDirName()` - returns `string` with the directory name of the plugin

### Inherited Singleton's static methods

* `getInstance()` - returns the instance of the Plugin class

### Abstract methods handling plugin's de/activation 

* `activation()` - executed while plugin activation
* `deactivation()` - executed while plugin deactivation

### Handling custom plugin's header fields

1. Add WordPress support for extra plugin's header fields using `extra_plugin_headers` filter:

```php
add_filter( 'extra_plugin_headers', function () {
    return [ 'License', 'License URI' ];
} );
```

2. Add methods to handle extra plugin's header fields:

```php
class Example extends Plugin {
    public static function getLicenseURI() {
        return self::get( 'License URI' );
    }
}
```

**NOTE:** `get` prefixed methods are automagically created for plugin's header fields that meet valid function name rules. e.g. `getLicense()` method.

## Requirements

PHP >= `7.4` version.

## License

[GPL 3.0](license.txt)