<?php

// models/BaseModel.php

class BaseModel extends Model
{
    public function get_site_data()
    {
        $sql = "SELECT * FROM site LIMIT 1";

        $statement = $this->run_query($sql);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_statistics_data($threshold)
    {
        $params = ['threshold' => $threshold];

        $sql = "SELECT * FROM users WHERE user_action_timestamp > :threshold";

        $statement = $this->run_query($sql, $params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_stickers_data()
    {
        $sql = "SELECT * FROM stickers";

        $statement = $this->run_query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sticker_by_sticker_name($sticker_name)
    {
        $params = ['sticker_name' => $sticker_name];

        $sql = "SELECT * FROM stickers WHERE sticker_name = :sticker_name LIMIT 1";

        $statement = $this->run_query($sql, $params);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_sticker_by_sticker_id($sticker_id)
    {
        $params = ['sticker_id' => $sticker_id];

        $sql = "SELECT * FROM stickers WHERE sticker_id = :sticker_id LIMIT 1";

        $statement = $this->run_query($sql, $params);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_user_by_user_name($user_name)
    {
        $params = ['user_name' => $user_name];

        $sql = "
            SELECT u.*, r.role_name, r.role_color
            FROM users u
            LEFT JOIN roles r ON u.user_role = r.role_id
            WHERE u.user_name = :user_name
            LIMIT 1
        ";

        $statement = $this->run_query($sql, $params);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_user_by_user_id($user_id)
    {
        $params = ['user_id' => $user_id];
    
        $sql = "
            SELECT u.*, r.role_name, r.role_color
            FROM users u
            LEFT JOIN roles r ON u.user_role = r.role_id
            WHERE u.user_id = :user_id
            LIMIT 1
        ";
    
        $statement = $this->run_query($sql, $params);
    
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_role_by_role_id($role_id)
    {
        $params = ['role_id' => $role_id];

        $sql = "SELECT * FROM roles WHERE role_id = :role_id LIMIT 1";

        $statement = $this->run_query($sql, $params);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_users()
    {
        $sql = "SELECT * FROM users ORDER BY user_creation_timestamp DESC";

        $statement = $this->run_query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_roles()
    {
        $sql = "SELECT * FROM roles";

        $statement = $this->run_query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_black_list()
    {
        $sql = "SELECT * FROM black_list";

        $statement = $this->run_query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_user_action_timestamp($user_name, $timestamp)
    {
        $params = [
            'user_name' => $user_name,
            'timestamp' => $timestamp
        ];

        $sql = "UPDATE users 
                SET user_action_timestamp = :timestamp 
                WHERE user_name = :user_name";

        $statement = $this->run_query($sql, $params);

        // rowCount -> returns line if it has been updated.

        return ($statement->rowCount() > 0);

    }

    public function get_posts_by_user_name($user_name, $limit = null, bool $count_only = false)
    {
        $where = "WHERE post_author = :user_name";
        $params = [':user_name' => $user_name];

        if ($count_only) {
            $sql = "SELECT COUNT(*) AS total FROM posts $where";
        } else {
            $sql = "
                SELECT *
                FROM posts
                $where
                ORDER BY post_timestamp DESC
            ";

            if (!empty($limit)) {
                $sql .= " LIMIT :limit";
                $params[':limit'] = (int) $limit;
            }
        }

        $statement = $this->run_query($sql, $params);

        return $count_only
            ? ($statement->fetch(PDO::FETCH_ASSOC)['total'] ?? 0)
            : $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}
