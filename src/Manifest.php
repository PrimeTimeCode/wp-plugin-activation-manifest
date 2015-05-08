<?php
namespace PrimeTime\WordPress\PluginManifest;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class Manifest
{
    private $data;


    /**
     * Manifest constructor.
     */
    public function __construct( $filepath )
    {
        try
        {
            $this->data = $this->sanitize( Yaml::parse($filepath) );
        }
        catch ( ParseException $e )
        {
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
    private function sanitize( $data )
    {
        $data = $this->filter_allowed_environments($data);

        return $this->filter_allowed_keys($data);
    }

    private function filter_by_key( $array, $allowed_keys )
    {
        return array_filter($array, function($key) use ($allowed_keys) {
            return in_array($key, $allowed_keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function filter_allowed_environments( $data )
    {
        $environments = [
            'global',
            WP_ENV
        ];

        $data = $this->filter_by_key($data, $environments);

        return $data;
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function filter_allowed_keys( $data )
    {
        $allowed_keys = [
            'enable',
            'disable',
            'network-enable',
            'network-disable'
        ];

        return array_map(function ( $value ) use ( $allowed_keys )
        {
            return $this->filter_by_key($value, $allowed_keys);
        }, $data);
    }
}