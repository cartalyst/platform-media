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
 * @version    6.0.6
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Platform\Media\Validator\MediaValidator;

class MediaValidatorTest extends PHPUnit_Framework_TestCase
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
    public function tearDown()
    {
        m::close();
    }

    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new MediaValidator(m::mock('Illuminate\Validation\Factory'));
    }

    /** @test */
    public function it_can_validate()
    {
        $rules = [
            'name' => 'required',
        ];

        $this->assertEquals($rules, $this->validator->getRules());

        $this->validator->onUpdate();
    }
}
