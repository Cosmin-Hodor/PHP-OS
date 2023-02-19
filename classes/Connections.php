<?php
/**
* 2020 C. Hodor - Open Source Community Platform
*/

/** 
* Connect detine toate metodele de conexiune necesare.
* @since 1.0 
*/

class Connect extends ConnectionSetup
{
    private static 
    $instance;

    protected 
    $db,

    $query,
    $count,
    $results,
    $data,
    $_error = false,

    $database,
    $hostname,
    $db_user,
    $db_pass;


    /**
     * Initializeaza baza de date.
     *
     * @param array  $db Returneaza o arie cu baza noastra de date.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    private function __construct()
    {
        if(is_null($this->database))
        {
            $this->db_get();
        }

        try 
        {
            $this->db = new PDO("mysql:host={$this->hostname};dbname={$this->database}",$this->db_user,$this->db_pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'", PDO::ATTR_PERSISTENT => true));
        } catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    /**
     * Instanteaza baza de date, returneaza un obiect singleton.
     *
     * @param object  $instance Instanta obiectului nostru.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public static function init()
    {
        if (!self::$instance)
        {
            self::$instance = new Connect();
        }
        return self::$instance;
    }

    
    /**
     * Preia configuratia pentru baza de date.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function db_get()
    {
        $this->db = NULL;
        $this->dataSet = NULL;
        $this->sqlQuery = NULL;

        $db_setup = ConnectionSetup::database();
        $this->database = $db_setup->dbName;
        $this->hostname = $db_setup->serverName;
        $this->db_user = $db_setup->userName;
        $this->db_pass = $db_setup->userPass;
        $db_setup = NULL;
    }

    
    /**
     * Executa un query SQL si returneaza rezultatul.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function query($sql, $params = array())
    {
        $this->_error = false;

        if ($this->query = $this->db->prepare($sql))
        {
            $i = 1;
            if (count($params))
            {
                foreach ($params as $param)
                {
                    $this->query->bindValue($i, $param);
                    $i++;
                }
            }

            if ($this->query->execute())
            {
                $this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
                $this->count = $this->query->rowCount();
            }   
            else
            {
                $this->_error = true;
            }
        }
        return $this;
    }

    /**
     * Executa o actiune in baza noastra de date.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    private function action($action, $table, $where = array())
    {
        if (count($where) === 3)
        {
            $operators = array('=', '>', '<', '>=', '<=');

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            if (in_array($operator, $operators))
            {
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

                if (!$this->query($sql, array($value))->error())
                {
                    return $this;
                }
            }
        }
        return false;
    }

    /**
     * Introduce un camp in baza de date.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function insert($table, $fields = array())
    {
        $keys = array_keys($fields);
        $values = null;
        $i = 1;

        foreach ($fields as $field)
        {
            $values .= '?';

            if ($i < count($fields)) $values .= ', ';
            $i++;
        }

        $sql = "INSERT INTO {$table} (`" . implode('`,`', $keys) . "`) VALUES ({$values})";

        if (!$this->query($sql, $fields)->error())
        {
            return true;
        }
        return false;
    }

    /**
     * Actualizeaza un camp in baza de date.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function update($table, $id, $fields)
    {
        $set = null;
        $i = 1;

        foreach ($fields as $name => $value)
        {
            $set .= "{$name} = ?";

            if ($i < count($fields)) 
            {
                $set .= ', ';
            }
            
            $i++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE ID = {$id}";

        if(!$this->query($sql, $fields)->error()) 
        {
            return true;
        }
        
        return false;
    }


    public function vote($tip, $id)
    {

        switch ($tip)
        {
            case -1:
                $this->query("UPDATE posts SET Votes = Votes - 2 WHERE ID = {$id}");
                return true;
            break;

            case 0:
                $this->query("UPDATE posts SET Votes = Votes - 1 WHERE ID = {$id}");
                return true;
            break;

            case 1:
                $this->query("UPDATE posts SET Votes = Votes + 1 WHERE ID = {$id}");
                return true;
            break;

            case 2:
                $this->query("UPDATE posts SET Votes = Votes + 2 WHERE ID = {$id}");
                return true;
            break;
        }
        return false;
    }

    /**
     * Sterge un camp in baza de date.
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function delete($table, $where) 
    {
        return $this->action('DELETE ', $table, $where);
    }


    /**
     * Returneaza un camp din baza de date.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function get($field, $table, $where = null) 
    {
        return $this->action("SELECT {$field}", $table, $where);
    }

    public function select($field, $table, $limit = false)
    {
        switch ($limit)
        {
            case false:
                return $this->query("SELECT {$field} FROM {$table}");
            break;

            case $limit > 0:
                return $this->query("SELECT {$field} FROM {$table} ORDER BY ID DESC limit {$limit}");
            break;

            default:
                return $this->query("SELECT {$field} FROM {$table}");
            break;
        }
    }

    public function data()
    {
        return $this->data;
    }

    public function seen($id)
    {
        $sql = "UPDATE users SET Last_Seen=now() WHERE ID = {$id}";
        $this->query($sql);
    }

    /**
     * Returneaza rezultatul unui query.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function results() 
    {
        return $this->results;
    }

    /**
     * Returneaza primul rezultat al unui query.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function first() 
    {
        $data = $this->results();
        return $data[0];
    }

    /**
     * Returneaza numarul de coloane.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function count() 
    {
        return $this->count;
    }

    /**
     * Returneaza erorile.
     *
     *
     * @since   1.0
     * @version 1.0 Versiunea Initiala
     */
    public function error() 
    {
        return $this->_error;
    }
}

