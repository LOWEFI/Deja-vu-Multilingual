<?php

// models/AccountModel.php

class AccountModel extends BaseModel
{
    public function update_account($user_name, $data)
    {
        $fields = [];

        $params = ['user_name' => $user_name];

        foreach ($data as $key => $value) {
            $fields[] = "`$key` = :$key";
            $params[$key] = $value;
        }

        $sql = "UPDATE `users` SET " . implode(', ', $fields) . " WHERE `user_name` = :user_name";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);

    }
}