<?php
/**
 * Created by PhpStorm.
 * User: mf196
 * Date: 07.09.2018
 * Time: 15:42
 */

namespace Main\Tools;


class Other
{

    static private $email = ['mfranciszczak@asist.pl', 'eokroj@asist.pl', 'kczopek@asist.pl', 'fkaminski@asist.pl', 'raporty@asist.pl'];
    static private $myErrorsCode = [

    ];

    static public function sendErrorMail(\Exception $e, string $message, string $title, string $code = '9999', $prefix = '')
    {
        $message .= self::$myErrorsCode["$code"] . "\n <br>";
        $id = isset($_GET['id']) ? clean($_GET['id']) : '';
        $message .= "\n Agent: ".$_SESSION['agent_id']." \n User:{$_SESSION['user_id']} \n";
        $message .= "\n <br>ADRES :".$_SERVER['REQUEST_URI']."\n <br>";
        $message .= 'Wiadomość ( '.$id.' '.date('Y-m-d H:i:s').' ) :' . $e->getMessage() . "\r\n <br> w pliku: " . $e->getFile() . "\r\n <br> w lini " . $e->getLine() . "\r\n <br> Szczegóły: <pre>" . $e->getTraceAsString() . '</pre>';
        $message .= "<br> \n".self::trace();
        $headers = "Content-Type: text/html; charset=UTF-8";
        if($_SERVER['HTTP_HOST'] != 'panel.asist.pl')
            $demo = '[AsistDemoError] ';
        else
            $demo = '[AsistError] ';

        $subject = $demo . $prefix .' '. ' ' . $title;
        $subject = preg_replace('/\s+/', ' ', $subject);
        mail(self::emailList(), $subject, $message, $headers);

        Log::WriteLogDB(str_replace('<br>', '', $message), $code);
        return true;
    }

    static public function sendErrorMailSample(string $message, string $title)
    {
        $message .= "\n Agent : " . $_SESSION['agent_id'];
        $message .= "\n Uzytkownik : " . $_SESSION['user_id']."\n <br>";
        $id = isset($_GET['id']) ? clean($_GET['id']) : '';
        $message .= "\n ID : " .$id." ".date('Y-m-d H:i:s')." \n <br>";
        $message .= self::trace();
        $headers = "Content-Type: text/html; charset=UTF-8";
        if($_SERVER['HTTP_HOST'] != 'panel.asist.pl')
            $demo = '[AsistDemoError] ';
        else
            $demo = '[AsistError] ';
        mail(self::emailList(), $demo . ' ' . $title, $message, $headers);
        Log::WriteLogDB(str_replace('<br>', '', $message), 9999);
    }

    static public function viewVar($var)
    {
        if(access('','','4')) {
            echo "<pre>\n============ \n";
            echo self::trace();
            echo "\n============\n</pre>";

            echo "<pre>\n####################### \n";
            print_r($var);
            echo "\n####################### \n</pre>";
        }
        return true;
    }

    static public function trace($i=0){
            $msg = "";
        foreach (debug_backtrace() as $key => $item) {
            if($i > 0){
                $msg .= "Induction :" . $i . "<br>\n";
                $msg .= "File : " . $item["file"] . "<br>\n";
                $msg .= "Line : " . $item["line"] . "<br>\n";
                $msg .= isset($item["class"]) ? "Class : " . $item["class"] . "<br>\n" : "";
                $msg .= isset($item["type"]) ? "Type : <b>". $item["type"] . "</b><br>\n" : "";
                $msg .= "Funktion / method : " . $item["function"] . "<br>\n";
                $msg .= "Arguments : <br>\n";
                foreach ($item["args"] as $index => $arg) {
                    if (is_array($arg)) {
                        $msg .=  "Array <br>";
                    } else {
                        $msg .=  $index . " => " . $arg . "<br>";
                    }
                }
                $msg .=  "<hr>";
            }
            $i++;
            }
        return $msg;
    }


    static public function emailList()
    {
        return implode(',', self::$email);
    }

} 