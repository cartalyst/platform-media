<?php

/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Media extension
 * @version    3.1.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Styles;

class Style
{
    /**
     * The Style name.
     *
     * @var string
     */
    public $name;

    /**
     * The Style attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Accessor for the "macros" attribute.
     *
     * @param  array  $macros
     * @return array
     */
    public function getMacrosAttribute($macros)
    {
        return $macros ?: [];
    }

    /**
     * Mutator for the "macros" attribute.
     *
     * @param  array  $macros
     * @return void
     */
    public function setMacrosAttribute(array $macros)
    {
        foreach (array_unique($macros) as $macro) {
            $this->attributes['macros'][] = $macro;
        }
    }

    /**
     * Dynamically retrieve attributes from the object.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $method = 'get'.ucfirst($key).'Attribute';

        $value = array_get($this->attributes, $key, null);

        if (method_exists($this, $method)) {
            return $this->{$method}($value);
        }

        return $value;
    }

    /**
     * Dynamically set attributes on the object.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $method = 'set'.ucfirst($key).'Attribute';

        if (method_exists($this, $method)) {
            $this->{$method}($value);
        } else {
            $this->attributes[$key] = $value;
        }
    }
}
