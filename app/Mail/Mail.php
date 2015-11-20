<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Mail;


use Manager\Exception\MailException;
use Manager\Support\Config;

class Mail
{
    /**
     * @var \PHPMailer
     */
    protected $mailer;

    /**
     * ### Sets the basic PHPMailer settings and content
     *
     * Mail constructor.
     * @param array $to
     * @param string $subject
     * @param string $content
     */
    public function __construct($to = [], $subject = '', $content = '')
    {
        $this->mailer = new \PHPMailer();
        $this->protocol(Config::get('mail', 'protocol'));
        $this->mailer->Host = Config::get('mail', 'host');
        $this->mailer->Port = Config::get('mail', 'port');
        $this->mailer->Username = Config::get('mail', 'username');
        $this->mailer->Password = Config::get('mail', 'password');
        $this->mailer->SMTPSecure = Config::get('mail', 'encryption');

        $this->mailer->setFrom(Config::get('mail', 'from')[0], Config::get('mail', 'from')[1]);

        $this->mailer->addAddress($to[0], $to[1]);
        $this->mailer->isHTML(true);

        $this->mailer->Subject = $subject;
        $this->mailer->Body = $content;
    }

    /**
     * ### Sets the protcol to be used by PHPMailer
     *
     * @param string $protocol
     */
    private function protocol($protocol)
    {
        switch ($protocol) {
            case 'smtp':
                if (Config::get('mail', 'username')) {
                    $this->mailer->SMTPAuth = true;
                } else {
                    $this->mailer->SMTPAuth = false;
                }

                $this->mailer->isSMTP();
                break;
            case 'php':
                $this->mailer->isSendmail();
                break;
            default:
                if (Config::get('mail', 'username')) {
                    $this->mailer->SMTPAuth = true;
                } else {
                    $this->mailer->SMTPAuth = false;
                }
                $this->mailer->isSMTP();
        }
    }

    /**
     * ### Adds a recipient
     *
     * @param string $mail
     * @param string $name
     * @return $this
     */
    public function addRecipient($mail, $name = null)
    {
        $this->mailer->addAddress($mail, $name);
        return $this;
    }

    /**
     * ### Adds a CC recipient
     *
     * @param string $mail
     * @param string $name
     * @return $this
     */
    public function addCC($mail, $name = null)
    {
        $this->mailer->addCC($mail, $name);
        return $this;
    }

    /**
     * ### Adds a BCC recipient
     *
     * @param string $mail
     * @param string $name
     * @return $this
     */
    public function addBCC($mail, $name = null)
    {
        $this->mailer->addBCC($mail, $name);
        return $this;
    }

    /**
     * ### Attaches a file
     *
     * @param mixed $file
     * @param string $name
     * @return $this
     * @throws \phpmailerException
     */
    public function attach($file, $name = null)
    {
        $this->mailer->addAttachment($file, $name);
        return $this;
    }

    /**
     * ### Sets alternative content for non-HTML clients
     *
     * @param string $text
     */
    public function altContent($text = '')
    {
        $this->mailer->AltBody = $text;
    }

    /**
     * ### Sends the prepared mail
     *
     * @return bool
     * @throws MailException
     * @throws \phpmailerException
     */
    public function send()
    {
        if (!$this->mailer->send()) {
            throw new MailException($this->mailer->ErrorInfo);
        }
        return true;
    }
}