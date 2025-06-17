<?php

// models/ManagementModel.php

class ManagementModel extends Model
{

    public function update_site($site_data)
    {
        $fields = [];
        $params = [];

        foreach ($site_data as $key => $value) {

            if ($value !== null) {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (!empty($fields)) {
            $sql = "UPDATE site SET " . implode(', ', $fields) . " WHERE site_id = 1";
            $statement = $this->run_query($sql, $params);

            return ($statement->rowCount() > 0);

        }
    }

    public function add_sticker($sticker_name, $sticker_location)
    {
        $params = [
            'sticker_name' => $sticker_name,
            'sticker_location' => $sticker_location
        ];

        $sql = "INSERT INTO stickers (sticker_name, sticker_location) VALUES (:sticker_name, :sticker_location)";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);

    }

    public function update_sticker($sticker_id, $sticker_name)
    {
        $params = [
            'sticker_name' => $sticker_name,
            'sticker_id' => $sticker_id
        ];

        $sql = "UPDATE stickers 
                SET sticker_name = :sticker_name
                WHERE sticker_id = :sticker_id";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function delete_sticker($sticker_id)
    {
        $params = [
            'sticker_id' => $sticker_id
        ];

        $sql = "DELETE FROM stickers WHERE sticker_id = :sticker_id";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function add_category($category_name, $category_priority)
    {
        $params = [
            'category_name' => $category_name,
            'category_priority' => $category_priority
        ];

        $sql = "INSERT INTO categories (category_name, category_priority) VALUES (:category_name, :category_priority)";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function update_category($category_id, $category_name, $category_language, $category_priority)
    {
        $params = [
            'category_name' => $category_name,
            'category_priority' => $category_priority,
            'category_language' => $category_language,
            'category_id' => $category_id
        ];

        $sql = "UPDATE categories 
                SET category_name = :category_name, 
                    category_priority = :category_priority,
                    category_language = :category_language
                WHERE category_id = :category_id";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function delete_category($category_id)
    {
        $params = [
            'category_id' => $category_id
        ];

        $sql = "DELETE FROM categories WHERE category_id = :category_id";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function update_user_row($columns, $conditions)
    {
        $fields = [];
        $params = [];

        foreach ($columns as $column_name => $column_value) {
            $fields[] = "$column_name = :columns_$column_name";
            $params["columns_$column_name"] = $column_value;
        }

        $where_clauses = [];

        foreach ($conditions as $condition_column => $condition_value) {
            $where_clauses[] = "$condition_column = :conditions_$condition_column";
            $params["conditions_$condition_column"] = $condition_value;
        }

        if (!empty($fields) && !empty($where_clauses)) {
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE " . implode(' AND ', $where_clauses);

            $statement = $this->run_query($sql, $params);

            return ($statement->rowCount() > 0);
        }

    }

    public function delete_user($user_name)
    {
        $params = [
            'user_name' => $user_name
        ];

        $sql = "DELETE FROM users WHERE user_name = :user_name";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function delete_user_posts($user_name)
    {
        $params = [
            'user_name' => $user_name
        ];

        $sql = "DELETE FROM posts WHERE post_author = :user_name";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }


    public function delete_user_threads($user_name)
    {
        $params = [
            'user_name' => $user_name
        ];

        $sql = "DELETE FROM threads WHERE thread_author = :user_name";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);

    }

    public function add_black_list($black_list_term)
    {

        $params = [
            ':black_list_term' => $black_list_term
        ];

        $sql = "INSERT INTO black_list (black_list_term) VALUES (:black_list_term)";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function delete_black_list($black_list_term)
    {
        $params = [
            ':black_list_term' => $black_list_term
        ];

        $sql = "DELETE FROM black_list WHERE black_list_term = :black_list_term";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function add_role($role_name, $role_color)
    {
        $params = [
            'role_name' => $role_name,
            'role_color' => $role_color
        ];

        $sql = "INSERT INTO roles (role_name, role_color) VALUES (:role_name, :role_color)";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }

    public function update_roles($roleId, $roleData)
    {
        $fields = [];
        $params = [];
        foreach ($roleData as $key => $value) {
            if ($value !== null) {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        if (!empty($fields)) {
            $sql = "UPDATE roles SET " . implode(', ', $fields) . " WHERE role_id = :role_id";
            $params['role_id'] = $roleId;
            $statement = $this->run_query($sql, $params);
            return ($statement->rowCount() > 0);
        }
        return false;
    }

    public function update_role($user_name, $user_role)
    {
        $params = [
            'user_name' => $user_name,
            'user_role' => $user_role
        ];

        $sql = "UPDATE users 
                SET user_role = :user_role
                WHERE user_name = :user_name";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
    }


    public function delete_sqlite_sequence()
    {
        $sql = "DELETE FROM sqlite_sequence";
    
        $statement = $this->run_query($sql);
    
        return ($statement->rowCount() > 0);
    }

}