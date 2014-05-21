<?php namespace Platform\Media\Models;
/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Media extension
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Database\Eloquent\Model;

class Media extends Model {

	/**
	 * {@inheritDoc}
	 */
	public $table = 'media';

	/**
	 * {@inheritDoc}
	 */
	protected $fillable = [
		'name',
		'path',
		'extension',
		'mime',
		'is_image',
		'size',
		'width',
		'height',
		'private',
		'groups',
		'tags',
		'thumbnail',
	];

	/**
	 * Get mutator for the "groups" attribute.
	 *
	 * @param  mixed  $groups
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getGroupsAttribute($groups)
	{
		if ( ! $groups)
		{
			return [];
		}

		if (is_array($groups))
		{
			return $groups;
		}

		if ( ! $_groups = json_decode($groups, true))
		{
			throw new InvalidArgumentException("Cannot JSON decode groups [{$groups}].");
		}

		return $_groups;
	}

	/**
	 * Set mutator for the "groups" attribute.
	 *
	 * @param  array  $groups
	 * @return void
	 */
	public function setGroupsAttribute($groups)
	{
		// If we get a string, let's just ensure it's a proper JSON string
		if ( ! is_array($groups))
		{
			$groups = $this->getGroupsAttribute($groups);
		}

		if ( ! empty($groups))
		{
			$groups = array_values(array_map('intval', $groups));
			$this->attributes['groups'] = json_encode($groups);
		}
		else
		{
			$this->attributes['groups'] = '';
		}
	}

	/**
	 * Get mutator for the "tags" attribute.
	 *
	 * @param  mixed  $tags
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getTagsAttribute($tags)
	{
		if ( ! $tags)
		{
			return [];
		}

		if (is_array($tags))
		{
			return $tags;
		}

		if ( ! $_tags = json_decode($tags, true))
		{
			throw new InvalidArgumentException("Cannot JSON decode tags [{$tags}].");
		}

		return $_tags;
	}

	/**
	 * Set mutator for the "tags" attribute.
	 *
	 * @param  array  $tags
	 * @return void
	 */
	public function setTagsAttribute($tags)
	{
		// If we get a string, let's just ensure it's a proper JSON string
		if ( ! is_array($tags))
		{
			$tags = $this->getTagsAttribute($tags);
		}

		if ( ! empty($tags))
		{
			$this->attributes['tags'] = json_encode($tags);
		}
		else
		{
			$this->attributes['tags'] = '';
		}
	}

}
