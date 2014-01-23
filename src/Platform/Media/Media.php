<?php namespace Platform\Media;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
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
	protected $fillable = array(
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
	);

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
			return array();
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

	public function generateUniqueId($in)
	{
		$in    = $in .time().rand();
		$out   = '';
		$index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$base  = strlen($index);

		for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--)
		{
			$bcp = bcpow($base, $t);
			$a   = floor($in / $bcp) % $base;
			$out = $out . substr($index, $a, 1);
			$in  = $in - ($a * $bcp);
		}

		return $out;
	}

}
