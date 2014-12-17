<?php namespace Platform\Media\Controllers\Admin;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Config;
use Illuminate\Database\Eloquent\Collection;
use Filesystem;
use Platform\Access\Controllers\AdminController;
use Platform\Foundation\Mailer;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Platform\Users\Repositories\RoleRepositoryInterface;
use Platform\Users\Repositories\UserRepositoryInterface;
use Sentinel;

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
	 * The Users Roles repository.
	 *
	 * @var \Platform\Users\Repositories\RoleRepositoryInterface
	 */
	protected $roles;

	/**
	 * The media configuration.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
	 * @param  \Platform\Users\Repositories\UserRepositoryInterface  $users
	 * @param  \Platform\Users\Repositories\RoleRepositoryInterface  $roles
	 * @return void
	 */
	public function __construct(
		MediaRepositoryInterface $media,
		UserRepositoryInterface $users,
		RoleRepositoryInterface $roles
	)
	{
		parent::__construct();

		$this->media = $media;

		$this->users = $users;

		$this->roles = $roles;

		$this->config = Config::get('platform/media::config');
	}

	/**
	 *
	 *
	 * @param  mixed  $id
	 * @return \Illuminate\View\View
	 */
	public function index($id)
	{
		if ( ! $items = $this->getEmailItems($id))
		{
			return redirect()->toAdmin('media');
		}

		if ($remove = input('remove'))
		{
			$items = implode(',', array_diff(explode(',', $id), [$remove])) ?: 0;

			return redirect()->toAdmin("media/{$items}/email");
		}

		$total = array_sum(array_map(function($item)
		{
			return $item->size;
		}, $items));

		$users = $this->users->findAll();

		$roles = $this->roles->findAll();

		return view('platform/media::email', compact('items', 'total', 'users', 'roles'));
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
			return redirect()->toAdmin('media');
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

		// Prepare the email view
		# probably have a dropdown where we can select a view..
		$view = 'platform/media::emails/email';

		// Prepare the email subject
		$subject = input('subject', array_get($this->config, 'email.subject'));

		// Get the email body
		$body = input('body');

		// Prepare the recipients
		$recipients = new Collection;

		foreach (input('users', []) as $email)
		{
			if ($user = $this->users->findByEmail($email))
			{
				$recipients->add($user);
			}
		}

		foreach (input('roles', []) as $roleId)
		{
			if ($role = $this->roles->find($roleId))
			{
				foreach ($role->users as $user)
				{
					$recipients->add($user);
				}
			}
		}

		if ($recipients->isEmpty())
		{
			$message = "You haven't selected any recipients.";

			$this->alerts->error($message);

			return redirect()->toAdmin("media/{$id}/email");
		}

		// Prepare the attachments
		$attachments = array_filter(array_map(function($attachment)
		{
			return [
				Filesystem::get($attachment->path)->getFullpath(),
				[
					'mime' => $attachment->mime,
				],
			];
		}, $items));

		// set input var, will make accessible to the view
		$input = input()->except(['_token', 'users']);

		$mailer = new Mailer;
		$mailer->setView($view, compact('body'));
		$mailer->setSubject($subject);
		$mailer->setAttachments($attachments);

		$mailer->addTo(Sentinel::getUser()->email, Sentinel::getUser()->name);

		foreach ($recipients as $recipient)
		{
			$mailer->addBcc($recipient->email, "{$recipient->first_name} {$recipient->last_name}");
		}

		$mailer->send();

		$this->alerts->success('Email was succesfully sent.');

		return redirect()->toAdmin('media');
	}

	protected function getEmailItems($id)
	{
		return array_filter(array_map(function($item)
		{
			return $this->media->find($item);
		}, explode(',', $id)));
	}

}
