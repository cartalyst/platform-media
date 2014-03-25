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
	 *
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
	 * Holds all the email attachments.
	 *
	 * @var array
	 */
	protected $attachments = [];

	/**
	 * Holds all the email data attachments.
	 *
	 * @var array
	 */
	protected $dataAttachments = [];

	/**
	 * Returns the from address.
	 *
	 * @return string
	 */
	public function getFromAddress()
	{
		return array_get($this->from, 'address', null);
	}

	/**
	 * Sets the from address.
	 *
	 * @param  string  $address
	 * @return self
	 */
	public function setFromAddress($address)
	{
		$this->from['address'] = $address;

		return $this;
	}

	/**
	 * Returns the from name.
	 *
	 * @return string
	 */
	public function getFromName()
	{
		return array_get($this->from, 'name', null);
	}

	/**
	 * Sets the from name.
	 *
	 * @param  string  $name
	 * @return self
	 */
	public function setFromName($name)
	{
		$this->from['name'] = $name;

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
	 * Set multiple To recipients.
	 *
	 * @param  array  $recipients
	 * @return self
	 */
	public function setTo(array $recipients = [])
	{
		return $this->setRecipients('to', $recipients);
	}

	/**
	 * Set a single To recipient.
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
	 * Set multiple Cc recipients.
	 *
	 * @param  array  $recipients
	 * @return self
	 */
	public function setCc(array $recipients = [])
	{
		return $this->setRecipients('cc', $recipients);
	}

	/**
	 * Set a single Cc recipient.
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
	 * Set multiple Bcc recipients.
	 *
	 * @param  array  $recipients
	 * @return self
	 */
	public function setBcc(array $recipients = [])
	{
		return $this->setRecipients('bcc', $recipients);
	}

	/**
	 * Set a single Bcc recipient.
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
	 * @return bool
	 */
	public function send()
	{
		return Mail::send($this->view, $this->data, $this->prepare());
	}

	public function queue()
	{
		return Mail::queue($this->view, $this->data, $this->prepare());
	}

	public function queueOn($queueName)
	{
		return Mail::queueOn($queueName, $this->view, $this->data, $this->prepare());
	}

	public function later($seconds)
	{
		return Mail::later($seconds, $this->view, $this->data, $this->prepare());
	}

	protected function prepare()
	{
		return function($mail)
		{
			$mail->subject($this->subject);

			$fromAddress = array_get($this->from, 'address', Config::get('mail.from.address'));

			$fromName = array_get($this->from, 'name', Config::get('mail.from.name'));

			$mail->from($fromAddress, $fromName);

			foreach (array_get($this->recipients, 'to', []) as $recipient)
			{
				$mail->to($recipient['email'], $recipient['name']);
			}

			foreach (array_get($this->recipients, 'cc', []) as $recipient)
			{
				$mail->cc($recipient['email'], $recipient['name']);
			}

			foreach (array_get($this->recipients, 'bcc', []) as $recipient)
			{
				$mail->bcc($recipient['email'], $recipient['name']);
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

	protected function setRecipients($type, array $recipients = [])
	{
		foreach ($recipients as $recipient)
		{
			$email = array_get($recipient, 'email');

			$name = array_get($recipient, 'name');

			$this->setRecipient($type, $email, $name);
		}

		return $this;
	}

	protected function setRecipient($type, $email, $name)
	{
		$this->recipients[$type][] = compact('email', 'name');

		return $this;
	}

}
