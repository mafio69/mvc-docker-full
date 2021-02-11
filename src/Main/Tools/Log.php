<?php
namespace Main\Tools;


class Log
{
    public static function saveLog(string $path,string $request ,string $response,string $name,$kat ='', $test= null) : void
    {
        $id = isset($_GET['id']) ? $_GET['id'] : '_brak';
        $shortId =  substr($id, -5);
        $log_dir = $path . date('Y-m-d').'_'.$kat;
        $log_code =  $_SESSION['agent_id'] . '_' .$_SESSION['user_id'] . '_' . str_replace('.', '_', microtime(true)).'_'.$shortId;
        $log_file = $log_code . '_' . preg_replace("/[^A-Za-z]/", '', $name) . '.txt';

        self::WriteApiCall($log_dir, $log_file, __FILE__.date('Y-m-d H:i:s').' server = '.$test.' id = '.$id.' = '."\n\n=====REQUEST=====\n" .  $request);
        self::WriteApiCall($log_dir, $log_file, "\n\n=====RESPONSE=====\n" . print_r($response, true) . "\n=====END=====\n");
    }

    public static function WriteApiCall($log_dir, $log_file, $string) : void
    {
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0775, true);
        }
        file_put_contents($log_dir . '/' . $log_file, $string, FILE_APPEND);
    }

    public static function WriteLogDB(string $message, string $code) : bool
    {
        $addArray = [
            'pk_log_agent_id' => $_SESSION['agent_id'],
            'pk_log_user_id' => $_SESSION['user_id'],
            'pk_log_ip' => $_SERVER['REMOTE_ADDR'],
            'pk_log_message' => addslashes($message),
            'pk_log_created' => date('Y-m-d H:i:s'),
            'pk_log_code' => $code
        ];

        return DataBase::insert('pk_log', null, $addArray);
    }
}