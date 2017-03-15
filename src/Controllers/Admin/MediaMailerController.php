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
 * @version    5.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Controllers\Admin;

use Config;
use Sentinel;
use Filesystem;
use Cartalyst\Support\Mailer;
use Illuminate\Database\Eloquent\Collection;
use Platform\Access\Controllers\AdminController;
use Platform\Roles\Repositories\RoleRepositoryInterface;
use Platform\Users\Repositories\UserRepositoryInterface;
use Platform\Media\Repositories\MediaRepositoryInterface;

class MediaMailerController extends AdminController
{
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
     * @var \Platform\Roles\Repositories\RoleRepositoryInterface
     */
    protected $roles;

    /**
     * The media configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The mailer instance.
     *
     * @var \Cartalyst\Support\Mailer
     */
    protected $mailer;

    /**
     * Constructor.
     *
     * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
     * @param  \Platform\Users\Repositories\UserRepositoryInterface  $users
     * @param  \Platform\Roles\Repositories\RoleRepositoryInterface  $roles
     * @param  \Cartalyst\Support\Mailer  $mailer
     * @return void
     */
    public function __construct(
        MediaRepositoryInterface $media,
        UserRepositoryInterface $users,
        RoleRepositoryInterface $roles,
        Mailer $mailer
    ) {
        parent::__construct();

        $this->media = $media;

        $this->users = $users;

        $this->roles = $roles;

        $this->config = Config::get('platform-media');

        $this->mailer = $mailer;
    }

    /**
     * Displays the main mailing page.
     *
     * @param  mixed  $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        if (! $items = $this->getEmailItems($id)) {
            return redirect()->route('admin.media.all');
        }

        if ($remove = input('remove')) {
            $items = implode(',', array_diff(explode(',', $id), [$remove])) ?: 0;

            return redirect()->route('admin.media.email', $items);
        }

        $total = array_sum(array_map(function ($item) {
            return $item->size;
        }, $items));

        $users = $this->users->findAll();

        $roles = $this->roles->findAll();

        return view('platform/media::email', compact('items', 'total', 'users', 'roles'));
    }

    /**
     * Processes the mailing request.
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\Response
     */
    public function process($id)
    {
        if (! $items = $this->getEmailItems($id)) {
            return redirect()->route('admin.media.all');
        }

        $maxAttachments = array_get($this->config, 'email.max_attachments');

        $maxAttachmentsSize = array_get($this->config, 'email.attachments_max_size');

        $total = array_sum(array_map(function ($item) {
            return $item->size;
        }, $items));

        $error = null;

        if (count($items) > $maxAttachments) {
            $error = "You've exceeded the max of total allowed attachments.";
        } elseif ($total > $maxAttachmentsSize) {
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
        $recipients = new Collection();

        foreach (input('users', []) as $email) {
            if ($user = $this->users->findByEmail($email)) {
                $recipients->add($user);
            }
        }

        foreach (input('roles', []) as $roleId) {
            if ($role = $this->roles->find($roleId)) {
                foreach ($role->users as $user) {
                    $recipients->add($user);
                }
            }
        }

        if ($recipients->isEmpty()) {
            $message = "You haven't selected any recipients.";

            $this->alerts->error($message);

            return redirect()->route('admin.media.email', $id);
        }

        // Prepare the attachments
        $attachments = array_filter(array_map(function ($attachment) {
            return [
                Filesystem::get($attachment->path)->getFullpath(),
                [
                    'mime' => $attachment->mime,
                ],
            ];
        }, $items));

        // set input var, will make accessible to the view
        $input = input()->except(['_token', 'users']);

        $this->mailer->setView($view, compact('body'));
        $this->mailer->setSubject($subject);
        $this->mailer->setAttachments($attachments);

        $this->mailer->addTo(Sentinel::getUser()->email, Sentinel::getUser()->name);

        foreach ($recipients as $recipient) {
            $this->mailer->addBcc($recipient->email, "{$recipient->first_name} {$recipient->last_name}");
        }

        $this->mailer->send();

        $this->alerts->success('Email was succesfully sent.');

        return redirect()->route('admin.media.all');
    }

    protected function getEmailItems($id)
    {
        return array_filter(array_map(function ($item) {
            return $this->media->find($item);
        }, explode(',', $id)));
    }
}
