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
use Media;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Media\Mailer;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Platform\Users\Repositories\GroupRepositoryInterface;
use Platform\Users\Repositories\UserRepositoryInterface;
use Redirect;
use View;

class MediaMailerController extends AdminController {

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

	protected $config;

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

		$this->config = Config::get('platform/media::config');
	}

	/**
	 *
	 *
	 * @param  mixed  $id
	 * @return \Illuminate\View\View
	 */
	public function email($id)
	{
		if ( ! $items = $this->getEmailItems($id))
		{
			return Redirect::toAdmin('media');
		}

		if ($remove = Input::get('remove'))
		{
			$items = implode(',', array_diff(explode(',', $id), [$remove])) ?: 0;

			return Redirect::toAdmin("media/{$items}/email");
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
	public function process($id)
	{
		if ( ! $items = $this->getEmailItems($id))
		{
			return Redirect::toAdmin('media');
		}

		$maxAttachments = array_get($this->config, 'email.max_attachments');

		$maxAttachmentsSize = array_get($this->config, 'email.attachments_max_size');

		$total = array_sum(array_map(function($item)
		{
			return $item->size;
		}, $items));

		$error = null;

		if (count($items) > $maxAttachments)
		{
			$error = "You've exceeded the max of total allowed attachments.";
		}
		elseif ($total > $maxAttachmentsSize)
		{
			$error = "You've exceeded the max allowed size of attachments.";
		}

		$view = "platform/media::emails/email";

		$subject = "Some subject";

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
				[
					'mime' => $attachment->mime,
				],
			];
		}, $items));

		$mailer = new Mailer;
		$mailer->setView($view);
		$mailer->setSubject($subject);
		$mailer->setAttachments($attachments);

		foreach ($recipients as $recipient)
		{
			$mailer->addBcc($recipient->email, "{$recipient->first_name} {$recipient->last_name}");
		}

		$mailer->send();

		return Redirect::toAdmin('media')->withSuccess('Email was succesfully sent.');
	}

	protected function getEmailItems($id)
	{
		return array_filter(array_map(function($item)
		{
			return $this->media->find($item);
		}, explode(',', $id)));
	}

}
