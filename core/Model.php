<?php

// core/Model.php

class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::get_instance()->get_connection();
    }

    protected function run_query($sql, $params = [])
    {
        $statement = $this->db->prepare($sql);
        if (!$statement) {
            throw new Exception("!!! Prepare SQL Request. !!!");
        }
        if (!$statement->execute($params)) {
            throw new Exception("!!! Execute SQL Request. !!!");
        }
        return $statement;
    }

}
