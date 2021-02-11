<?php

namespace Main\Bootstrap;

use \PDO;
use \PDOException;

class Model

{
    use Views;

    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $dbh;
    private $stmt;
    protected $monolog;
    protected $mailer;
    protected $message;

    /**
     * Database constructor.
     *
     */
    public function __construct()
    {
        $time_start = microtime(true);
        $injObj = Injection::injectSwiftMailer();
        $this->mailer = $injObj->mailer;
        $this->message = $injObj->message;
        unset($injObj);
        $this->monolog = Injection::injectMonolog($this->mailer, $this->message, 'model');
        $this->monolog->info('Utworzono obiekt Modelu zainicjowano Monolog ');

        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASSWORD;
        $this->dbname = DB_NAME;

        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8';
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            if (ENV_DEV) {
                $time_end = microtime(true);
                $time = $time_end - $time_start;
                $time = round($time, 6);
                echo '<pre>Nie udało się wykonać metody ' . __METHOD__ . "\n O czasie wykonywania: \n" . $time . "s \n Linia: \n" . $e->getLine() . "\n W pliku: \n" . $e->getFile() . "\n Trace: \n" . $e->getTraceAsString() . "</pre>";
                $this->monolog->error('SQL Nie udało się połączyć z bazą w konstruktorze ( time: ' . $time . " )");
            } else {
                $time_end = microtime(true);
                $time = $time_end - $time_start;
                $time = round($time, 6);
                $this->monolog->error('SQL Nie udało się połączyć z bazą w konstruktorze ( time: ' . $time . " )");
                $this->view('error/error.twig');
            }
        }

    }

    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    public function lastId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * @param string $tableName
     * @param string|null $order
     * @param null $limit
     * @param bool $debug
     * @return array
     */

    public function getAll(string $tableName, string $order = null, $limit = null, bool $debug = false): array
    {
        $time_start = microtime(true);
        $addLimit = $limit === null ? '' : ' LIMIT  ' . $limit;
        $orderBy = '';
        if ($order !== null) {
            $orderBy = "ORDER BY  " . $order . " DESC ";
        }
        $sql = "SELECT * FROM " . $tableName . " " . $orderBy . " " . $addLimit;


        if ($debug === true)
            echo $sql;

        //$sql = 'SELECT * FROM pk_request_tmp';
        $this->stmt = $this->dbh->prepare($sql);

        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($time_start, $e, __METHOD__, $sql);
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->logNumRows($sql, $time);
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param bool $debug
     * @param string $table
     * @param array $whereData
     * @return array|bool|mixed
     */

    public function search(string $table, array $whereData, bool $debug = null): array
    {
        $time_start = microtime(true);
        $sql = "SELECT * FROM $table WHERE ";

        foreach ($whereData as $ksql => $vsql) {
            $sql .= '' . $ksql . ' = :' . $ksql . '  ';
        }

        if ($debug === true)
            echo $sql;

        $this->stmt = $this->dbh->prepare($sql);
        foreach ($whereData as $k => $dat) {

            switch (true) {

                case is_bool($dat):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($dat):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($dat):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }

            $this->stmt->bindValue(':' . $k, $dat, $type);
        }
        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($time_start, $e, __METHOD__, $sql);
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->logNumRows($sql, $time);

        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param string $table
     * @param array $whereData
     * @param bool|null $debug
     * @return object
     */
    public function getFirst(string $table, array $whereData, bool $debug = null): object
    {
        $time_start = microtime(true);
        $sql = "SELECT * FROM $table WHERE ";

        foreach ($whereData as $ksql => $vsql) {
            $sql .= '' . $ksql . ' = :' . $ksql . '  AND ';
        }
        $sql = substr($sql, 0, -4);
        $sql .= ' LIMIT 1 ';

        if ($debug === true)
            echo $sql;

        $this->stmt = $this->dbh->prepare($sql);
        foreach ($whereData as $k => $dat) {

            switch (true) {

                case is_bool($dat):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($dat):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($dat):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }

            $this->stmt->bindValue(':' . $k, $dat, $type);
        }
        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($time_start, $e, __METHOD__, $sql);
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->logNumRows($sql, $time);
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @param string $sql
     * @param array $data
     * @param bool $debug
     * @return array
     */
    public function query(string $sql, array $data, bool $debug = false): array
    {
        $time_start = microtime(true);
        if ($debug === true)
            echo $sql;

        $this->stmt = $this->dbh->prepare($sql);
        foreach ($data as $k => $dat) {

            switch (true) {

                case is_bool($dat):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($dat):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($dat):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }

            $this->stmt->bindValue(':' . $k, $dat, $type);
        }
        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($time_start, $e, __METHOD__, $sql);
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->logNumRows($sql, $time);

        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * @param string $sql
     * @param array $whereData
     * @param bool $debug
     * @return array|bool|mixed
     * Klucz w tablicy $whereData powinien być taki sam jak jak :placeholder
     */

    public function find(string $sql, array $whereData, bool $debug = false)
    {
        $time_start = microtime(true);
        if ($debug === true)
            echo $sql;

        $this->stmt = $this->dbh->prepare($sql);
        foreach ($whereData as $k => $dat) {

            switch (true) {

                case is_bool($dat):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($dat):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($dat):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }

            $this->stmt->bindValue(':' . $k, $dat, $type);
        }
        try {
            $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($time_start, $e, __METHOD__, $sql);
        }
        $test = $this->stmt->rowCount();
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->logNumRows($sql, $time);
        if ($test == 1)
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        elseif ($test > 1)
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        else
            return false;
    }


    /**
     * @param string $tableName
     * @param array $data
     * @param string $where
     * @param array $whereData
     * @param bool $debug
     * @return array|bool
     */
    public function update(string $tableName, array $data, string $where, array $whereData, bool $debug = false)
    {

        $sql = "UPDATE " . $tableName . " SET ";
        foreach ($data as $k => $field) {
            $sql .= $k . " =  :" . $k . ", ";
        }
        $sql = rtrim($sql, ', ');
        $sql .= ' WHERE ' . $where;

        if ($debug === true)
            echo $sql;

        $this->stmt = $this->dbh->prepare($sql);

        foreach ($data as $k => $dat) {

            switch (true) {

                case is_bool($dat):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($dat):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($dat):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }
            $this->stmt->bindValue(':' . $k, $dat, $type);
        }

        foreach ($whereData as $k => $dat) {

            switch (true) {

                case is_bool($dat):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($dat):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($dat):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }

            $this->stmt->bindValue(':' . $k, $dat, $type);
        }
        $time_start = microtime(true);

        try {
            $data = $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($time_start, $e, __METHOD__, $sql);
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $this->logNumRows($sql, $time);
        return $data;

    }

    /**
     * @param string $tableName
     * @param array $data
     * @param bool $debug
     * @return array|bool
     */
    public function insert(string $tableName, array $data, bool $debug = false): bool
    {

        $time_start = microtime(true);
        $sql = "INSERT INTO " . $tableName . " (";
        foreach ($data as $k => $field) {
            $sql .= $k . ", ";
        }

        $sql = rtrim($sql, ', ');
        $sql .= " ) VALUES ( ";
        foreach ($data as $k => $field) {
            $sql .= ":" . $k . ", ";
        }

        $sql = $sql = rtrim($sql, ', ');
        $sql .= " )";

        if ($debug === true)
            echo $sql;

        $this->stmt = $this->dbh->prepare($sql);
        foreach ($data as $k => $dat) {

            switch (true) {

                case is_bool($dat):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_int($dat):
                    $type = PDO::PARAM_INT;
                    break;
                case is_null($dat):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }

            $this->stmt->bindValue(':' . $k, $dat, $type);
        }
        try {
            $data = $this->stmt->execute();
        } catch (PDOException $e) {
            $this->prepareException($time_start, $e, __METHOD__, $sql);
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;

        $this->logNumRows($sql, $time);
        return $data;

    }


    /**
     * @param string $sql
     * @param float $time
     * @return bool
     */
    public function logNumRows(string $sql, float $time): bool
    {
        $numrowstresc = $_SERVER["REMOTE_ADDR"] . ";" . URL . ";Records" . $this->rowCount() . ";" . $time . ";" . $sql;

        $myFile = "log_db";
        $classLog = $time > 1 ? " WARNING " : " INFO ";
        $fh = fopen(LOG_DB . DIRECTORY_SEPARATOR . $myFile . "_" . date("Y-m-d_H") . ".log", 'a') or die("can't open file");
        fwrite($fh, date("Y-m-d H:i:s") . " - " . $classLog . $numrowstresc . "\r\n");
        fclose($fh);

        $myFileSlow = "slow_db";
        if ($time > 1) {
            $fh = fopen(LOG_DB . DIRECTORY_SEPARATOR . $myFileSlow . "_" . date("Y-m-d_H") . ".txt", 'a') or die("can't open file");
            fwrite($fh, date("Y-m-d H:i:s") . " - " . $classLog . $numrowstresc . "\r\n");
            fclose($fh);
        }

        return true;
    }

    /**
     * @param float $time_start
     * @param PDOException $e
     * @param string $method
     * @param string $sql
     * @return bool
     */
    public function prepareException(float $time_start, PDOException $e, string $method, string $sql): bool
    {

        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $time = round($time, 6);
        $Log = $_SERVER["REMOTE_ADDR"] . ";" . URL . ";Records" . $this->rowCount() . ";" . $time . ";" . $sql;
        if (ENV_DEV) {
            $myFile = "log_db";
            $fh = fopen(LOG_DB . DIRECTORY_SEPARATOR . $myFile . "_" . date("Y-m-d_H") . ".log", 'a') or die("can't open file");
            fwrite($fh, date("Y-m-d H:i:s") . " - " . " ERROR " . $Log . "\r\n");
            fclose($fh);
            $this->monolog->error('SQL Nie udało się wykonać metody ' . $method . ' time: ' . $time . ' Message: ' . $e->getMessage() . ' SQL ' . $sql . ' File ' . $e->getFile() . ' Line :' . $e->getLine() . ' Trace: ' . $e->getTraceAsString());
            echo '<pre>Nie udało się wykonać metody ' . __METHOD__ . "\n O czasie wykonywania: \n" . $time . "s \n Linia: \n" . ' Message: ' . $e->getMessage() . "\n In file: \n" . $e->getFile() . "\n In line: \n" . $e->getLine() . "\n Trace: \n" . $e->getTraceAsString() . "</pre>";
            exit;
        } else {
            $this->monolog->error('SQL Nie udało się wykonać metody ' . $method . ' time: ' . $time . ' Message: ' . $e->getMessage() . ' SQL ' . $sql . ' File ' . $e->getFile() . ' Line :' . $e->getLine() . ' Trace: ' . $e->getTraceAsString());
            $this->view('error/error.twig');
        }
        return true;
    }
}