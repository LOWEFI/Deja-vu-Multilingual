<?php

// models/SignupModel.php

class SignupModel extends BaseModel
{
    public function signup($data)
    {
        $params = [
            'user_name_raw' => $data['user_name_raw'],
            'user_name' => $data['user_name'],
            'user_password' => $data['user_password'],
            'user_description' => $data['user_description'],
            'user_role' => $data['user_role'],
            'user_ban' => $data['user_ban'],
            'user_action_timestamp' => $data['user_action_timestamp'],
            'user_creation_timestamp' => $data['user_creation_timestamp'],
            'user_kick_timestamp' => $data['user_kick_timestamp'],
            'user_language' => $data['user_language']
        ];

        $sql = "INSERT INTO users (user_name_raw, user_name, user_password, user_description, user_role, user_ban, user_action_timestamp, user_creation_timestamp, user_kick_timestamp, user_language) VALUES (:user_name_raw, :user_name, :user_password, :user_description, :user_role, :user_ban, :user_action_timestamp, :user_creation_timestamp, :user_kick_timestamp, :user_language)";

        $this->run_query($sql, $params);

        return $this->db->lastInsertId();
    }

}