<?php
namespace Main\Bootstrap;

use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;
use \Exception;

class Controller
{
    public $post;
    protected $monolog;
    protected $mailer;
    protected $message;
    public $put;
    public $patch;
    public $delete;
    public $get;

    public function __construct()
    {

        $injArray = Injection::injectSwiftMailer();
        $this->monolog = Injection::injectMonolog($injArray['mailer'], $injArray['message'], 'model');
        $this->monolog->info('Utworzono obiekt Kontrolera zainicjowano Monolog ');
        unset($injArray);
        $this->prepareRequest();
    }

    public function view(string $name, array $var)
    {

        $loader = new FilesystemLoader(BASE . '/App/views');
        $twig = new Environment($loader);
        try {
            $template = $twig->load($name);
        } catch (Exception $e) {
            if (ENV_DEV) {
                echo "URL: " . URL . " \nFile " . $e->getFile() . "Line: " . $e->getLine() . "\nMessage: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
                $this->monolog->error('VIEW Nie udało się załadować widoku o nazwie ' . $name . " \n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
                exit();
            } else {
                $this->monolog->error('VIEW Nie udało się załadować widoku o nazwie ' . $name . " \n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");

                mfRedirect("/");
                die('Błąd systemowy, zapraszamy później (problem z załodowaniem widoku)');

            }
        }
        echo $template->render($var);
        return true;
    }

    public function prepareRequest()
    {

        if (isset($_POST) && is_array($_POST)) {
            foreach ($_POST as $key => $val) {
                $this->post[clean($key)] = clean($val);
            }
        }
        $this->post = (object)$this->post;

        if (isset($_GET) && is_array($_GET)) {
            foreach ($_GET as $key => $val) {
                $this->get[clean($key)] = clean($val);
            }
        }
        $this->get = (object)$this->get;

        $method = isset($_SERVER['REQUEST_METHOD']) ?? 'POST';

        $method === "PUT" ? parse_str(file_get_contents('php://input', false, null, -1, $_SERVER['CONTENT_LENGTH']), $put) : $put = array();

        if (isset($put) && is_array($put)) {
            foreach ($put as $key => $val) {
                $this->put[clean($key)] = clean($val);
            }
        }
        $this->put = (object)$this->put;

        $method === "PATCH" ? parse_str(file_get_contents('php://input', false, null, -1, $_SERVER['CONTENT_LENGTH']), $patch) : $patch = array();

        if (isset($patch) && is_array($patch)) {
            foreach ($patch as $key => $val) {
                $this->patch[clean($key)] = clean($val);
            }
        }
        $this->patch = (object)$this->patch;

        $method === "DELETE" ? parse_str(file_get_contents('php://input', false, null, -1, $_SERVER['CONTENT_LENGTH']), $delete) : $delete = array();

        if (isset($delete) && is_array($delete)) {
            foreach ($delete as $key => $val) {
                $this->delete[clean($key)] = clean($val);
            }
        }
        $this->delete = (object)$this->delete;
    }

    public function getJson($data)
    {
        return json_decode($data);
    }

    public function sentJson($data)
    {
        return json_encode($data);
    }

}