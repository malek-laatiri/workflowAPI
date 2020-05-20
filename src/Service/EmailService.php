<?php


namespace App\Service;


use App\Entity\User;

class EmailService
{
    private $mailer;

    /**
     * EmailGenerator constructor.
     * @param $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(User $user,$pass)
    {
        $messages = (new \Swift_Message('test'))
            ->setFrom('testagence6@gmail')
            ->setTo($user->getEmail())
            ->setBody("your username ".$user->getUsername()." you passsword ".$pass);


        return $this->mailer->send($messages);
    }
    public function passwordChanged(User $user)
    {
        $messages = (new \Swift_Message('test'))
            ->setFrom('testagence6@gmail')
            ->setTo($user->getEmail())
            ->setBody("Dear username ".$user->getUsername()." you passsword has just changed");


        return $this->mailer->send($messages);
    }
    public function sendEmailAsigned(string $email)
    {
        $messages = (new \Swift_Message('test'))
            ->setFrom('testagence6@gmail')
            ->setTo($email)
            ->setBody("you have new notification");


        return $this->mailer->send($messages);
    }
}