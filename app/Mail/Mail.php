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

    public function addRecipient($mail, $name = null)
    {
        $this->mailer->addAddress($mail, $name);
        return $this;
    }

    public function addCC($mail, $name = null)
    {
        $this->mailer->addCC($mail, $name);
        return $this;
    }

    public function addBCC($mail, $name = null)
    {
        $this->mailer->addBCC($mail, $name);
        return $this;
    }

    public function attach($file, $name = null)
    {
        $this->mailer->addAttachment($file, $name);
        return $this;
    }

    public function altContent($text = '')
    {
        $this->mailer->AltBody = $text;
    }

    public function send()
    {
        if (!$this->mailer->send()) {
            throw new MailException($this->mailer->ErrorInfo);
        }
        return true;
    }
}