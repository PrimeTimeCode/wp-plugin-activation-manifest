<?php
namespace PrimeTime\WordPress\PluginManifest;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class Manifest
{
    protected $filepath;
    
    protected $environment;

    protected $data;

    protected static $allowed_keys = [
        'enable',
        'disable',
        'network-enable',
        'network-disable'
    ];


    /**
     * Manifest constructor.
     */
    public function __construct( $filepath, $environment = null )
    {
        $this->filepath = realpath($filepath);
        $this->environment = $environment;
    }

    public function load()
    {
        try {
            $this->data = $this->sanitize(Yaml::parse($this->filepath));
        } catch ( ParseException $e ) {
            wp_die("<h1>Error parsing $filepath</h1>" . $e->getMessage(), 'Plugin Manifest Error');
        }
    }

    /**
     * @return array
     */
    public function get_data()
    {
        return $this->data;
    }

    /**
     * Sanitize the parsed data
     *
     * @param $data
     *
     * @return array
     */
    protected function sanitize( $data )
    {
        $data = $this->filter_allowed_environments($data);

        return $this->filter_allowed_keys($data);
    }

    /**
     * [filter_by_key description]
     * @param  [type] $array        [description]
     * @param  [type] $allowed_keys [description]
     * @return [type]               [description]
     */
    protected function filter_by_key( $array, $allowed_keys )
    {
        return array_filter($array, function($key) use ($allowed_keys) {
            return in_array($key, $allowed_keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Reduces data to a set limited to keys of the allowed environments
     * 
     * @param $data
     *
     * @return array
     */
    protected function filter_allowed_environments( $data )
    {
        return $this->filter_by_key($data, $this->environments());
    }

    /**
     * Returns an array of environment keys
     * 
     * @return [type] [description]
     */
    protected function environments()
    {
        return array_filter([
            'global',
            $this->environment
        ]);
    }

    /**
     * Filter the values of the data to only allow for
     * allowed key:values 
     * 
     * @param $data
     *
     * @return array
     */
    protected function filter_allowed_keys( $data )
    {
        return array_map(function ( $value ) {
            return $this->filter_by_key($value, static::$allowed_keys);
        }, $data);
    }
}