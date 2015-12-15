# WP Plugin Activation Manifest

Lock down the activation state of individual plugins based on environment with minimal effort.

## Usage

Create a YAML file that defines the plugin activation state you want to enforce.  Name it whatever you wish and put it wherever you want (ideally outside of the webroot).  Add this file to your project's repository.

Example manifest file:
```yml
# top level keys are the environment
some_environment:
  enable:
    - someplugin/someplugin.php
  disable:
    - someotherplugin/someotherplugin.php
  # supports multisite
  network-enable:
    - someplugin/someplugin.php
  network-disable:
    - someplugin/someplugin.php

# 'global' is a special key that you can use to apply to all environments
global:
  enable:
    - woocommerce/woocommerce.php
    - wpmandrill/wpmandrill.php
    
development:
  disable:
    - wpmandrill/wpmandrill.php
    
```

Install
`composer require primetime/wp-plugin-activation-manifest`

Execute the mandate
```php
require('vendor/autoload.php');
// ...
// after WordPress is loaded - eg: within an mu-plugin
// ..
\PrimeTime\WordPress\PluginManifest\Activation::set('path/to/plugin-manifest.yml', getenv('WP_ENV'));
```
In the example above, `WP_ENV` is an environment variable defining the name of the environment (eg: development, staging, production).
This environment name should match to a top-level key in the yaml file.

Environent configuration is applied after the `global` configuration and will take precedence over it.

That's it!
