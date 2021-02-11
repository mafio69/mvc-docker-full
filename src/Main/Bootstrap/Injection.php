<?php
namespace Main\Bootstrap;

use DateTimeZone;
use Exception;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Injection
{
    /**
     *
     */
    public static $monolog;

    public static function injectSwiftMailer()
    {
        $transporter = new Swift_SmtpTransport(SM_HOST, SM_PORT, SM_ENCY);
        $transporter->setUsername(SM_USER);
        $transporter->setPassword(SM_PASSWORD);
        $mailer = new Swift_Mailer($transporter);
        $message = (new Swift_Message('Błąd w MFMVC'))
            ->setFrom(['mariusz@doe.com' => 'System MFMVC'])
            ->setTo(['szymonfranciszczak@gmail.com','mf1969@gmail.com'])
            ->setBody('My body', 'text/html', 'UTF-8');

        return (object)['mailer' => $mailer,'message' =>$message];
   }

    public static function injectMonolog(Swift_Mailer $mailer, Swift_Message $message, $channel)
    {
        self::$monolog = new Logger($channel);
        self::$monolog::setTimezone(new DateTimeZone('Europe/Warsaw'));

        try {
            self::$monolog->pushHandler(new StreamHandler(LOG_PATH . '/app.log', Logger::DEBUG,true));
            $emailHandler = new SwiftMailerHandler($mailer, $message, Logger::ERROR, true);
            $emailHandler->setFormatter(new HtmlFormatter());
            self::$monolog->pushHandler($emailHandler);
        } catch (Exception $e) {
            if(ENV_DEV){
                echo $e->getMessage();
            }else{
                $message = (new Swift_Message('Błąd monologa '.$channel))
                    ->setFrom(['mariusz@doe.com' => 'System MFMVC'])
                    ->setTo(['mf1969@gmail.com' => 'A name'])
                    ->setBody("Błąd systemu logowania, w  ".__class__.'->'.__METHOD__,'text/html','UTF-8');
                $mailer->send($message);
            }
        }
        return self::$monolog;
   }
}