# Appearance Customizer Composer Package (v1.x)

## Getting Started
After installing composer in your project, add the following repositories to your **composer.json** file:
```
{
    "type": "vcs",
    "url": "https://github.com/Fuel-Travel/package-appearance-customizer.git"
},
```

Then, run:

```
composer require fuel-travel/package-appearance-customizer:1.*
```

In your project, initialize the package:
```
require_once __DIR__ . '/vendor/autoload.php';
use FuelTravel\AppearanceCustomizer;
$appearance_customizer = new AppearanceCustomizer;
```
:tada:

## Class Usage
### Disable
The package is enabled by default. To disable the package call the **disable** method from an **init** action callback function. The priority must be **3 or lower**.
```
add_action( 'init', 'disable', 1 );
function disable() {
    global $appearance_customizer;
    $appearance_customizer->disable();
}
```
### Enable
The package is enabled by defalut, but if it was disabled and you would like to enable it again you can call the **enable** method from an **init** action callback function with a priority of **3 or lower**.
```
add_action( 'init', 'enable', 3 );
function enable() {
    global $appearance_customizer;
    $appearance_customizer->enable();
}
```

### __construct( $stylesheet_handle = '', $config = '' )
The class constructor accepts two arguments:
 - stylesheet_handle - *(string)* the handle of the stylesheet that the settings style should be added after<br />Default: The child theme name sanatized with dashes
 - config - *(array)* a configuration array for style targeting and default setting values<br />Default: The default configuration included with the package.

### enable_settings
All settings are neabled by default. However, it is best practice to define specifically which settings should be enabled so that when settings are added in the future they are not added to your production enviornment without testing.
```
$this->appearance_customizer->enable_settings = array(
    'heading_color',
    'text_section', // optional
    'entry_title_size',
    'entry_title_weight',
    'entry_content_clamp',
);
```
The section will automatically show if one of its child settings is enabled. However, you can enable it manually too.

## Configuration
To make your own configuration, copy the contents of the `src/default_config.php` file into a file in your plugin, edit it, and pass its contents to the constructor:
```
$config = require( 'path/to/my/customizer_config.php' );
$appearance_customizer = new AppearanceCustomizer( 'my-handle', $config );
```

### Default
The default value for the setting (do no include units).
The color default value should include the pound symbol.

### Target
A list of CSS selectors to use for the setting.

### Special Configuration
The line clamp configuration requires `font_size` and `line_height` configuration to properly perform clamping. In most cases, these should be set to your theme's default.