<?php namespace Platform\Media\Models;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use InvalidArgumentException;
use Cartalyst\Tags\TaggableTrait;
use Cartalyst\Tags\TaggableInterface;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Support\Traits\NamespacedEntityTrait;

class Media extends Model implements TaggableInterface {

	use NamespacedEntityTrait, TaggableTrait;

	/**
	 * {@inheritDoc}
	 */
	public $table = 'media';

	/**
	 * {@inheritDoc}
	 */
	protected $fillable = [
		'mime',
		'name',
		'path',
		'size',
		'private',
		'is_image',
		'extension',
		'thumbnail',
		'width',
		'height',
		'roles',
	];

	/**
	 * {@inheritDoc}
	 */
	protected static $entityNamespace = 'platform/media';

	/**
	 * Get mutator for the "roles" attribute.
	 *
	 * @param  mixed  $roles
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getRolesAttribute($roles)
	{
		if ( ! $roles)
		{
			return [];
		}

		if (is_array($roles))
		{
			return $roles;
		}

		if ( ! $_roles = json_decode($roles, true))
		{
			throw new InvalidArgumentException("Cannot JSON decode roles [{$roles}].");
		}

		return $_roles;
	}

	/**
	 * Set mutator for the "roles" attribute.
	 *
	 * @param  array  $roles
	 * @return void
	 */
	public function setRolesAttribute($roles)
	{
		// If we get a string, let's just ensure it's a proper JSON string
		if ( ! is_array($roles))
		{
			$roles = $this->getRolesAttribute($roles);
		}

		if ( ! empty($roles))
		{
			$roles = array_values(array_map('intval', $roles));
			$this->attributes['roles'] = json_encode($roles);
		}
		else
		{
			$this->attributes['roles'] = '';
		}
	}

}
