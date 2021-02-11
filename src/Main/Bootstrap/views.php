<?php
namespace Main\Bootstrap;

use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

trait Views
{
    public function view($name, array $var = null)
    {
        $injArray = Injection::injectSwiftMailer();
        $this->monolog = Injection::injectMonolog($injArray['mailer'], $injArray['message'], 'model');
        $loader = new FilesystemLoader(BASE . '/views');
        $twig = new Environment($loader);
        try {
            $template = $twig->load($name);
        } catch (Exception $e) {
            $this->monolog->error('Nie udało się załadować widoku o nazwie '.$name);
            if(ENV_DEV){
                echo $e->getMessage() . "\n<br>";
                echo $e->getTraceAsString() . "\n<br>";
                $this->monolog->Error('VIEW błąd w tracie views ' . " \n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
                exit() ;
            }else{
                $this->monolog->Error('VIEW błąd w tracie views ' . " \n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
                die('Błąd systemowy, zapraszamy później');
            }
        }
        echo $template->render($var);
        return;
    }
}