<?php namespace Platform\Media\Controllers\Frontend;
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
 * @version    1.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Support\Facades\Response;
use Platform\Foundation\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Filesystem\Laravel\Facades\Filesystem;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Platform\Media\Repositories\MediaRepositoryInterface;

class MediaController extends Controller {

	/**
	 * The Media repository.
	 *
	 * @var \Platform\Media\Repositories\MediaRepositoryInterface
	 */
	protected $media;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
	 * @return void
	 */
	public function __construct(MediaRepositoryInterface $media)
	{
		parent::__construct();

		$this->media = $media;
	}

	/**
	 * Returns the given media file.
	 *
	 * @param  string  $path
	 * @return \Illuminate\Http\Response
	 */
	public function view($path)
	{
		$media = $this->getMedia($path);

		$file = Filesystem::read($media->path);

        $ttl = config('platform/media::headers.max-age');
        $etag = md5($file);

        if ($etag === request()->server('HTTP_IF_NONE_MATCH'))
        {
            return response(null, 304);
        }
        else
        {
            $headers = [
                'Cache-Control'  => "max-age=$ttl, public",
                'Content-Type'   => $media->mime,
                'Content-Length' => strlen($file),
                'ETag'           => $etag,
            ];

            return $this->respond($file, $headers);
        }
	}

	/**
	 * Downloads the given media file.
	 *
	 * @param  string  $path
	 * @return \Illuminate\Http\Response
	 */
	public function download($path)
	{
		$media = $this->getMedia($path);

		$file = Filesystem::read($media->path);

		$headers = [
			'Connection'          => 'close',
			'Content-Type'        => $media->mime,
			'Content-Length'      => strlen($file),
			'Content-Disposition' => 'attachment; filename="'.$media->name.'"',
		];

		return $this->respond($file, $headers);
	}

	/**
	 * Grabs the media file by its path and determines if the
	 * logged in user  has access to the media file.
	 *
	 * @param  string  $path
	 * @return \Platform\Media\Media
	 */
	protected function getMedia($path)
	{
		if ( ! $media = $this->media->findByPath($path))
		{
			throw new HttpException(404, 'Media does not exist.');
		}

		if ($media->private)
		{
			$pass = false;

			if ($user = Sentinel::check())
			{
				$pass = true;

				$mediaRoles = $media->roles;

				$userRoles = $user->roles->lists('id');

				if ( ! empty($mediaRoles) and ! array_intersect($mediaRoles, $userRoles))
				{
					$pass = false;
				}
			}

			if ( ! $pass)
			{
				throw new HttpException(403, "You don't have permission to access this file.");
			}
		}

		return $media;
	}

	/**
	 * Sends the response with the appropriate headers.
	 *
	 * @param  string  $file
	 * @param  array  $headers
	 * @return \Illuminate\Http\Response
	 */
	protected function respond($file, $headers = [])
	{
		$response = Response::make($file, 200);

		foreach ($headers as $name => $value)
		{
			$response->header($name, $value);
		}

		return $response;
	}

}
