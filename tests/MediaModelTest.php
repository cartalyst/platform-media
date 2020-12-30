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
 * @version    10.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;
use Platform\Media\Models\Media;

class MediaModelTest extends TestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->addToAssertionCount(1);

        m::close();
    }

    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
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

        $this->assertSame(json_encode($roles), Arr::get($this->media->getAttributes(), 'roles'));

        $this->assertSame($roles, $this->media->roles);
    }

    /**
     * @test
     */
    public function it_throws_in_exception_if_roles_cannot_be_decoded()
    {
        $this->expectException('InvalidArgumentException');

        $roles = 'foo';

        $this->media->roles = $roles;

        $this->assertSame(json_encode($roles), Arr::get($this->media->getAttributes(), 'roles'));

        $this->assertSame($roles, $this->media->roles);
    }
}
