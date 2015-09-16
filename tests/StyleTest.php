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
 * @version    3.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Tests;

use PHPUnit_Framework_TestCase;
use Platform\Media\Styles\Style;

class StyleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
    {
        $this->style = new Style('foo');
    }

    /** @test */
    public function it_can_set_and_retrieve_macros_attribute()
    {
        $macros = [
            'resize',
        ];

        $this->style->macros = $macros;

        $this->assertEquals($macros, $this->style->macros);
    }

    /** @test */
    public function it_can_set_and_retrieve_attributes()
    {
        $attribute = 'foobar';

        $this->style->attribute = $attribute;

        $this->assertEquals($attribute, $this->style->attribute);
    }
}
