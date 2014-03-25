<?php namespace Platform\Media;

use Config;
use Mail;

class Mailer {

	/**
	 * The email subject.
	 *
	 * @var string
	 */
	protected $subject = null;

	/**
	 * The email from address.
	 *
	 * @var array
	 */
	protected $from = [];

	/**
	 * The email recipients.
	 *
	 * @var array
	 */
	protected $recipients = [];

	/**
	 * The email view.
	 *
	 * @var string
	 */
	protected $view;

	/**
	 * The email view data.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * The email attachments.
	 *
	 * @var array
	 */
	protected $attachments = [];

	/**
	 * The email data attachments.
	 *
	 * @var array
	 */
	protected $dataAttachments = [];

	/**
	 * Returns the from address.
	 *
	 * @return array
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * Sets the from address.
	 *
	 * @param  string  $from
	 * @return self
	 */
	public function setFrom($from)
	{
		$this->from = $from;

		return $this;
	}

	/**
	 * Returns the email subject.
	 *
	 * @return string
	 */
	public function getSubject()
	{
		return $this->subject;
	}

	/**
	 * Sets the email subject.
	 *
	 * @param  string  $subject
	 * @return self
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Returns all the email recipients.
	 *
	 * @param  string  $type
	 * @return array
	 */
	public function getRecipients($type = null)
	{
		return array_get($this->recipients, $type, $this->recipients);
	}

	/**
	 * Sets multiple "To" recipients.
	 *
	 * @param  array  $recipients
	 * @return self
	 */
	public function setTo(array $recipients = [])
	{
		return $this->setRecipients('to', $recipients);
	}

	/**
	 * Sets a single "To" recipient.
	 *
	 * @param  string  $email
	 * @param  string  $name
	 * @return self
	 */
	public function addTo($email, $name)
	{
		return $this->setRecipient('to', $email, $name);
	}

	/**
	 * Sets multiple "Cc" recipients.
	 *
	 * @param  array  $recipients
	 * @return self
	 */
	public function setCc(array $recipients = [])
	{
		return $this->setRecipients('cc', $recipients);
	}

	/**
	 * Sets a single "Cc" recipient.
	 *
	 * @param  string  $email
	 * @param  string  $name
	 * @return self
	 */
	public function addCc($email, $name)
	{
		return $this->setRecipient('cc', $email, $name);
	}

	/**
	 * Sets multiple "Bcc" recipients.
	 *
	 * @param  array  $recipients
	 * @return self
	 */
	public function setBcc(array $recipients = [])
	{
		return $this->setRecipients('bcc', $recipients);
	}

	/**
	 * Sets a single "Bcc" recipient.
	 *
	 * @param  string  $email
	 * @param  string  $name
	 * @return self
	 */
	public function addBcc($email, $name)
	{
		return $this->setRecipient('bcc', $email, $name);
	}

	/**
	 * Sets the email view and view data.
	 *
	 * @param  string  $view
	 * @param  array  $data
	 * @return self
	 */
	public function setView($view, $data = [])
	{
		$this->view = $view;

		$this->data = $data;

		return $this;
	}

	public function setAttachments($attachments)
	{
		$this->attachments = $attachments;

		return $this;
	}

	/**
	 * Sends the email.
	 *
	 * @return int
	 */
	public function send()
	{
		return Mail::send($this->view, $this->data, $this->prepareCallback());
	}

	/**
	 * Queue a new e-mail message for sending.
	 *
	 * @param  string  $queue
	 * @return int
	 */
	public function queue($queue = null)
	{
		return Mail::queue($this->view, $this->data, $this->prepareCallback(), $queue);
	}

	/**
	 * Queue a new e-mail message for sending on the given queue.
	 *
	 * @param  string  $queue
	 * @return int
	 */
	public function queueOn($queue)
	{
		return Mail::queueOn($queue, $this->view, $this->data, $this->prepareCallback());
	}

	/**
	 * Queue a new e-mail message for sending after (n) seconds.
	 *
	 * @param  int  $delay
	 * @return int
	 */
	public function later($delay)
	{
		return Mail::later($delay, $this->view, $this->data, $this->prepareCallback());
	}

	/**
	 * Prepares the email callback.
	 *
	 * @return \Closure
	 */
	protected function prepareCallback()
	{
		return function($mail)
		{
			$mail->subject($this->subject);

			$mail->from(
				array_get($this->from, 'address', Config::get('mail.from.address')),
				array_get($this->from, 'name', Config::get('mail.from.name'))
			);

			foreach ($this->recipients as $type => $recipients)
			{
				foreach ($recipients as $recipient)
				{
					$mail->{$type}($recipient['email'], $recipient['name']);
				}
			}

			foreach ($this->attachments as $attachment)
			{
				$options = [];

				if (is_array($attachment))
				{
					list($attachment, $options) = $attachment;
				}

				$mail->attach($attachment, $options);
			}

			foreach ($this->dataAttachments as $name => $data)
			{
				$options = [];

				if (is_array($data))
				{
					list($data, $options) = $data;
				}

				$mail->attachData($data, $name, $options);
			}
		};
	}

	/**
	 * Sets multiple recipients by type.
	 *
	 * @param  string  $type
	 * @param  array  $recipients
	 * @return self
	 */
	protected function setRecipients($type, array $recipients = [])
	{
		foreach ($recipients as $recipient)
		{
			$this->setRecipient(
				$type,
				array_get($recipient, 'email'),
				array_get($recipient, 'name')
			);
		}

		return $this;
	}

	/**
	 * Sets a single recipient by type.
	 *
	 * @param  string  $type
	 * @param  string  $email
	 * @param  string  $name
	 * @return self
	 */
	protected function setRecipient($type, $email, $name)
	{
		$this->recipients[$type][] = compact('email', 'name');

		return $this;
	}

}
