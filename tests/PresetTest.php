<?php

/*
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
 * @version    9.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Tests;

use PHPUnit\Framework\TestCase;
use Platform\Media\Styles\Preset;

class PresetTest extends TestCase
{
    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->style = new Preset('foo', []);
    }

    /** @test */
    public function it_can_set_and_retrieve_macros_attribute()
    {
        $macros = [
            'resize',
        ];

        $this->style->macros = $macros;

        $this->assertSame($macros, $this->style->macros);
    }

    /** @test */
    public function it_can_set_and_retrieve_attributes()
    {
        $attribute = 'foobar';

        $this->style->attribute = $attribute;

        $this->assertSame($attribute, $this->style->attribute);
    }
}
