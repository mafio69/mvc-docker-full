<?php
namespace Main\Tools;

class  DataBase
{


    public static function update(string $tableName, string $where, array $allowedFields = NULL, array $addArray = NULL, $debug = null, string $prefix = NULL)
    {
        $data = [];
        if ($allowedFields) {
            //$allowedFields = array_map(filter_var(),$allowedFields);
            $allowedFields = array_flip($allowedFields);
            $data = array_intersect_key($_POST, $allowedFields);
        }

        $dataEND = [];
        if ($prefix) {
            foreach ($data as $k => $v) {

                if (strstr($k, $prefix)) {
                    $k = str_ireplace($prefix, '', $k);
                    $dataEND[clean($k)] = clean($v);
                }
            }
        } else {
            foreach ($data as $k => $v) {
                $dataEND[clean($k)] = clean($v);
            }
        };

        if ($addArray) {
            $dataEND = array_merge($dataEND, $addArray);
        }

        if (empty($dataEND) && (isset($where) && empty($where)))
            return 'Brak danych lub klauzuli "WHERE" ';

        $sql = 'UPDATE ' . $tableName . ' SET ';
        foreach ($dataEND as $key => $value) {
            $sql .= $key . " = '" . $value . "' ,";
        }

        $len = strlen($sql);
        $sql = substr($sql, 0, ($len - 1));
        $sql .= ' WHERE ' . $where;

        if ( $debug == true)
            echo $sql;

        $result = mysqli_query_($sql) OR die(SQLerror($sql, mysqli_error($GLOBALS['dblink']), __LINE__, __FILE__));

        if (mysqli_affected_rows($GLOBALS['dblink'])) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function insert(string $tableName, array $allowedFields = null, array $addArray = null, $debug = null, string $prefix = null)
    {
        $data = [];
        if ($allowedFields) {
            //$allowedFields = array_map(clean(),$allowedFields);
            $allowedFields = array_flip($allowedFields);
            $data = array_intersect_key($_POST, $allowedFields);
        }
        //print_r($data);
        $dataEND = [];
        if ($prefix) {
            foreach ($data as $k => $v) {
                if (strstr($k, $prefix)) {
                    $k = str_ireplace($prefix, '', $k);
                    $dataEND[clean($k)] = clean($v);
                }
            }
        } else {

            foreach ($data as $k => $v) {
                $dataEND[clean($k)] = clean($v);

            }
        };

        if ($addArray) {
            $dataEND = $dataEND + $addArray;
        }
        if (empty($dataEND))
            return 'Brak danych';

        $sql_a = ' ( ';
        $sql_b = ' ( ';
        $sql = 'INSERT INTO ' . $tableName;
        foreach ($dataEND as $key => $value) {
            $sql_a .= $key . ' ,';
            $sql_b .= '\'' . $value . '\' ,';
        }
        $len = strlen($sql_b);
        $sql_b = substr($sql_b, 0, ($len - 1)); //Usunięcie ostatniego przycinka
        $len = strlen($sql_a);
        $sql_a = substr($sql_a, 0, ($len - 1)); //Usunięcie ostatniego przycinka
        $sql = $sql . $sql_a . ' ) VALUE ' . $sql_b . ' ) ';

        if ( $debug == true)
            echo $sql;


        $result = mysqli_query_($sql) OR die(SQLerror($sql, mysqli_error($GLOBALS['dblink']), __LINE__, __FILE__));

        if ($result) {
            return 1;
        } else {
            return 0;

        }
    }

    public static function query(string $sql, $debug = null)
    {

        if ( $debug === true)
            echo $sql;

        $result = mysqli_query_($sql) OR DIE(SQLerror($sql, mysqli_error($GLOBALS['dblink']), __LINE__, __FILE__));
        if (mysqli_num_rows($result) > 0) {

            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $test[$i] = ($row);
                $i++;
            }
            return ($test);
        } else {
            return [];
        }
    }

    public static function querySingle(string $sql , $debug = null)
    {

        $result = mysqli_query_($sql) or die(SQLerror($sql, mysqli_error($GLOBALS['dblink']), __LINE__, __FILE__));

        if ( $debug === true)
            echo $sql;

        if (mysqli_num_rows($result) == 1) {
            return $data[] = mysqli_fetch_assoc($result);
        } else {
            return [];
        }
    }

    public static function delete(string $table, string $where)
    {
        $query = "DELETE FROM $table WHERE $where";

       

        $result = mysqli_query_($query) or die(SQLerror($query, mysqli_error($GLOBALS['dblink']), __LINE__, __FILE__));
        if (mysqli_affected_rows($GLOBALS['dblink']) > 0)
            return 1;
        else
            return 0;
    }

    public static function lastId()
    {

        return mysqli_insert_id($GLOBALS['dblink']);
    }

    public static function queryCustom(string $sql, bool $debug = false)
    {
        $result = mysqli_query_($sql) or die(SQLerror($sql, mysqli_error($GLOBALS['dblink']), __LINE__, __FILE__));

        if($debug === true)
            echo $sql;

        return $result;
    }
}