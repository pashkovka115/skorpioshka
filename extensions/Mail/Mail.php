<?php


namespace Extensions\Mail;


use Core\Exceptions\MailException;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    protected $mailer;


    public function __construct()
    {
        $this->mailer = new PHPMailer(DEBUG);
        if (DEBUG) {
            $GLOBALS['mail_debug'] = '';
            $this->mailer->Debugoutput = function ($str, $level) {
                $GLOBALS['mail_debug'] .= "$level: $str<br>";
            };
        }
        $this->mailer->SMTPDebug = 2;
        $this->mailer->CharSet = 'UTF-8';
        if (MAIL_DRIVER == 'sendmail') {
            $this->mailer->isSendmail();
        } elseif (MAIL_DRIVER == 'smtp') {
            $this->mailer->isSMTP();
        }
        $this->mailer->Host = MAIL_HOST;
        $this->mailer->SMTPAuth = MAIL_AUTH;                                   // Enable SMTP authentication
        $this->mailer->Username = MAIL_USERNAME;                     // SMTP username
        $this->mailer->Password = MAIL_PASSWORD;                               // SMTP password
        $this->mailer->SMTPSecure = MAIL_SECURE;                                  // Enable TLS encryption, `ssl` also accepted
        $this->mailer->Port = MAIL_PORT;
//        $this->mailer->setFrom(MAIL_USERNAME);
//        $this->mailer->addAddress(MAIL_USERNAME); // получатель
        $this->mailer->isHTML(true);
    }


    public function send($subject = '', $message = '', $alt_message = '', $to = MAIL_RECIPIENT, $from = MAIL_USERNAME)
    {
        $this->mailer->Subject = $subject;
        $this->mailer->Body = $message;
        $this->mailer->AltBody = $alt_message;
        $this->mailer->addAddress($to); // получатель
        $this->mailer->setFrom($from); // от кого
        $this->mailer->send();
    }

    public function debug()
    {
        global $mail_debug;
        if (isset($mail_debug) and $mail_debug != '')
            echo '<br>Mailer Error: <br>' . $mail_debug;
        else
            echo '<br>Mailer Error: <br>' . $this->mailer->ErrorInfo;
    }


    /**
     * @return PHPMailer
     */
    public function getMailer(): PHPMailer
    {
        return $this->mailer;
    }

}