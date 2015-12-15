<?php
namespace PrimeTime\WordPress\PluginManifest;

use PrimeTime\WordPress\PluginControl\EnablePlugins;
use PrimeTime\WordPress\PluginControl\DisablePlugins;
use PrimeTime\WordPress\PluginControl\NetworkEnablePlugins;
use PrimeTime\WordPress\PluginControl\NetworkDisablePlugins;

class Activation
{
    protected $manifest;

    public function __construct( Manifest $manifest )
    {
        $this->manifest = $manifest;
        $this->apply();
    }

    public static function set( $config_file )
    {
        return new static(new Manifest($config_file));
    }

    protected function apply()
    {
        array_map([$this, 'enforce'], $this->manifest->get_data());
    }

    protected function enforce( $manifest )
    {
        if ( !empty($manifest[ 'enable' ]) )
            new EnablePlugins($manifest[ 'enable' ]);

        if ( !empty($manifest[ 'disable' ]) )
            new DisablePlugins($manifest[ 'disable' ]);

        if ( is_multisite() )
        {
            if ( !empty($manifest[ 'network-enable' ]) )
                new NetworkEnablePlugins($manifest[ 'network-enable' ]);

            if ( !empty($manifest[ 'network-disable' ]) )
                new NetworkDisablePlugins($manifest[ 'network-disable' ]);
        }
    }

}
