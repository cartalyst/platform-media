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
 * @version    5.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Tests;

use PHPUnit_Framework_TestCase;
use Platform\Media\Models\Media;

class MediaModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
    {
        $this->media = new Media();
    }

    /** @test */
    public function it_has_a_roles_mutator_and_accessor()
    {
        $roles = [
            1,
            2,
        ];

        $this->media->roles = $roles;

        $this->assertEquals(json_encode($roles), array_get($this->media->getAttributes(), 'roles'));

        $this->assertSame($roles, $this->media->roles);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_in_exception_if_roles_cannot_be_decoded()
    {
        $roles = 'foo';

        $this->media->roles = $roles;

        $this->assertEquals(json_encode($roles), array_get($this->media->getAttributes(), 'roles'));

        $this->assertSame($roles, $this->media->roles);
    }
}
