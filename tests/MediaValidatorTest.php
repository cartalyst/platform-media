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
 * @version    8.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Platform\Media\Validator\MediaValidator;

class MediaValidatorTest extends TestCase
{
    /**
     * Validator instance.
     *
     * @var \Platform\Media\Validator\MediaValidator
     */
    protected $validator;

    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new MediaValidator(m::mock('Illuminate\Validation\Factory'));
    }

    /** @test */
    public function it_can_validate()
    {
        $rules = [
            'name' => 'required',
        ];

        $this->assertSame($rules, $this->validator->getRules());

        $this->validator->onUpdate();
    }
}
