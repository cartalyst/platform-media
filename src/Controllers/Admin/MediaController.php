<?php namespace Platform\Media\Controllers\Admin;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Config;
use DataGrid;
use Illuminate\Database\Eloquent\Collection;
use Input;
use Lang;
use Mail;
use Media;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Platform\Users\Repositories\GroupRepositoryInterface;
use Platform\Users\Repositories\UserRepositoryInterface;
use Redirect;
use Response;
use Request;
use View;

class MediaController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
		//'update',
	];

	/**
	 * Media repository.
	 *
	 * @var \Platform\Media\Repositories\MediaRepositoryInterface
	 */
	protected $media;

	/**
	 * The Users repository.
	 *
	 * @var \Platform\Users\Repositories\UserRepositoryInterface
	 */
	protected $users;

	/**
	 * The Users Groups repository.
	 *
	 * @var \Platform\Users\Repositories\GroupRepositoryInterface
	 */
	protected $groups;

	/**
	 * Holds all the mass actions we can execute.
	 *
	 * @var array
	 */
	protected $actions = [
		'delete',
	];

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
	 * @param  \Platform\Users\Repositories\UserRepositoryInterface  $users
	 * @param  \Platform\Users\Repositories\GroupRepositoryInterface  $groups
	 * @return void
	 */
	public function __construct(
		MediaRepositoryInterface $media,
		UserRepositoryInterface $users,
		GroupRepositoryInterface $groups
	)
	{
		parent::__construct();

		$this->media = $media;

		$this->users = $users;

		$this->groups = $groups;
	}

	/**
	 * Display a listing of media files.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		// Get a list of all the available tags
		$tags = $this->media->getTags();

		// Get a list of all the available groups
		$groups = $this->groups->findAll();

		// Show the page
		return View::make('platform/media::index', compact('tags', 'groups'));
	}

	/**
	 * Datasource for the media Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->media->grid();

		$columns = [
			'id',
			'tags',
			'name',
			'mime',
			'path',
			'size',
			'private',
			//'groups',
			'is_image',
			//'extension',
			'thumbnail',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		return DataGrid::make($data, $columns, $settings);
	}

	/**
	 * Media upload form processing.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function upload()
	{
		$file = Input::file('file');

		$tags = Input::get('tags', []);

		if ($this->media->validForUpload($file))
		{
			if ($media = $this->media->upload($file, $tags))
			{
				return Response::json($media);
			}
		}

		return Response::json($this->media->getError(), 400);
	}

	/**
	 * Shows the form for updating a media.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		// Get the media information
		if ( ! $media = $this->media->find($id))
		{
			$message = Lang::get('platform/media::message.not_found', compact('id'));

			return Redirect::toAdmin('media')->withErrors($message);
		}

		// Get a list of all the available tags
		$tags = $this->media->getTags();

		// Get a list of all the available groups
		$groups = $this->groups->findAll();

		// Show the page
		return View::make('platform/media::form', compact('media', 'tags', 'groups'));
	}

	/**
	 * Processes the form for updating a media.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($id)
	{
		Input::merge(['groups' => Input::get('groups', [])]);

		$input = Input::except('file');

		if ($this->media->validForUpdate($id, $input))
		{
			if ($this->media->update($id, $input, Input::file('file')))
			{
				if (Request::ajax())
				{
					return Response::json('success');
				}

				$message = Lang::get('platform/media::message.success.update');

				return Redirect::toAdmin('media')->withSuccess($message);
			}
		}

		if (Request::ajax())
		{
			return Response::json($this->media->getError(), 400);
		}

		return Redirect::back()->withErrors($this->media->getError());
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
		$action = Input::get('action');

		if (in_array($action, $this->actions))
		{
			foreach (Input::get('entries', []) as $entry)
			{
				$this->media->{$action}($entry);
			}

			return Response::json('Success');
		}

		return Response::json('Failed', 500);
	}

	/**
	 *
	 *
	 * @param  mixed  $id
	 * @return \Illuminate\View\View
	 */
	public function email($id)
	{
		$items = $this->getEmailItems($id);

		if (empty($items))
		{
			return Redirect::toAdmin('media');
		}

		$total = array_sum(array_map(function($item)
		{
			return $item->size;
		}, $items));

		$users = $this->users->findAll();

		$groups = $this->groups->findAll();

		return View::make('platform/media::email', compact('items', 'total', 'users', 'groups'));
	}

	/**
	 *
	 *
	 * @param  mixed  $id
	 * @return \Illuminate\Http\Response
	 */
	public function processEmail($id)
	{
		$items = $this->getEmailItems($id);

		if (empty($items))
		{
			return Redirect::toAdmin('media');
		}

		$view = "platform/media::emails/email";

		$subject = "Some subject";

		$from = array(
			'email' => Config::get('mail.from.address'),
			'name'  => Config::get('mail.from.name')
		);

		// Prepare the recipients
		$recipients = new Collection;

		foreach (Input::get('users', []) as $email)
		{
			if ($user = $this->users->findByEmail($email))
			{
				$recipients->add($user);
			}
		}

		foreach (Input::get('groups', []) as $groupId)
		{
			if ($group = $this->groups->find($groupId))
			{
				foreach ($group->users as $user)
				{
					$recipients->add($user);
				}
			}
		}

		if ($recipients->isEmpty())
		{
			$message = "You haven't selected any recipients.";

			return Redirect::toAdmin("media/{$id}/email")->withErrors($message);
		}

		// Prepare the attachments
		$attachments = array_filter(array_map(function($attachment)
		{
			return [
				Media::getFile($attachment->path)->getFullpath(),
				['mime' => $attachment->mime],
			];
		}, $items));

		$mailer = new \Platform\Media\Mailer;
		$mailer->setView($view);
		$mailer->setSubject($subject);
		$mailer->setAttachments($attachments);

		foreach ($recipients as $recipient)
		{
			$mailer->addBcc($recipient->email, "{$recipient->first_name} {$recipient->last_name}");
		}

		$mailer->send();

		return;

		return Redirect::toAdmin('media')->withSuccess('Email succesfully sent.');
	}

	protected function getEmailItems($id)
	{
		return array_filter(array_map(function($item)
		{
			return $this->media->find($item);
		}, explode(',', $id)));
	}

}
