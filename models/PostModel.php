<?php // models/PostModel.php

class PostModel extends BaseModel
{

    public function create_post($data)
    {
        $params = [
            ':post_thread_id' => $data['post_thread_id'],
            ':post_content' => $data['post_content'],
            ':post_author' => $data['post_author'],
            ':post_timestamp' => $data['post_timestamp'],
            ':post_last_change_timestamp' => $data['post_last_change_timestamp'],
            ':post_hide' => $data['post_hide']
        ];

        $sql = "
            INSERT INTO posts (
                post_thread_id, 
                post_content, 
                post_author, 
                post_timestamp, 
                post_last_change_timestamp,
                post_hide
            ) VALUES (
                :post_thread_id, 
                :post_content, 
                :post_author, 
                :post_timestamp, 
                :post_last_change_timestamp,
                :post_hide
            )
        ";

        $this->run_query($sql, $params);

        return $this->db->lastInsertId();
    }

    public function get_post($post_id)
    {
        $params = [
            'post_id' => $post_id
        ];

        $sql = "SELECT * FROM posts WHERE post_id = :post_id LIMIT 1";

        $statement = $this->run_query($sql, $params);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_post_position_by_thread_id($thread_id, $post_id)
    {
        $params = [
            'thread_id' => $thread_id,
            'post_id' => $post_id
        ];

        $sql = "
            SELECT COUNT(*) + 1 AS position
            FROM posts
            WHERE post_thread_id = :thread_id
              AND post_timestamp < (
                  SELECT post_timestamp 
                  FROM posts 
                  WHERE post_id = :post_id
                  LIMIT 1
              )
        ";

        $statement = $this->run_query($sql, $params);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ? (int) $result['position'] : null;
    }

    public function get_posts($thread_id)
    {
        $params = [
            'thread_id' => $thread_id
        ];

        $sql = "SELECT * FROM posts WHERE post_thread_id = :thread_id ORDER BY post_timestamp ASC";

        $statement = $this->run_query($sql, $params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);

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

    public function get_posts_and_users($limit = null, $offset = null)
    {
        $sql = "
        SELECT 
            p.*,
            u.user_id,
            u.user_ban,
            t.thread_title,
            r.role_name,
            r.role_color
        FROM posts p
        LEFT JOIN users u ON p.post_author = u.user_name
        LEFT JOIN threads t ON p.post_thread_id = t.thread_id
        LEFT JOIN roles r ON u.user_role = r.role_id
        ORDER BY p.post_timestamp DESC
    ";

        if (!is_null($limit)) {
            $sql .= " LIMIT :limit";
            $params['limit'] = (int) $limit;

            if (!is_null($offset)) {
                $sql .= " OFFSET :offset";
                $params['offset'] = (int) $offset;
            }
        }

        $statement = $this->run_query($sql, $params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_posts_and_users_by_thread_id($thread_id, $limit = null, $offset = null)
    {

        $params = [
            'thread_id' => $thread_id
        ];

        $sql = "
        SELECT 
            p.*,
            u.user_id,
            u.user_ban,
            r.role_name,
            r.role_color
        FROM posts p
        LEFT JOIN users u ON p.post_author = u.user_name
        LEFT JOIN roles r ON u.user_role = r.role_id
        WHERE p.post_thread_id = :thread_id
        ORDER BY p.post_timestamp ASC
    ";

        if (!is_null($limit)) {
            $sql .= " LIMIT :limit";
            $params['limit'] = (int) $limit;

            if (!is_null($offset)) {
                $sql .= " OFFSET :offset";
                $params['offset'] = (int) $offset;
            }
        }

        $statement = $this->run_query($sql, $params);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count_posts_by_user_name($user_name)
    {
        $params = ['user_name' => $user_name];

        $sql = "
        SELECT COUNT(*) AS total
        FROM posts
        WHERE post_author = :user_name
        ";

        $statement = $this->run_query($sql, $params);
        return $statement->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function update_post_row($columns, $conditions)
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
            $sql = "UPDATE posts SET " . implode(', ', $fields) . " WHERE " . implode(' AND ', $where_clauses);

            $statement = $this->run_query($sql, $params);

            return ($statement->rowCount() > 0);

        }

    }

    public function delete_post($post_id)
    {
        $params = [
            'post_id' => $post_id,
        ];

        $sql = "DELETE FROM posts WHERE post_id = :post_id";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);

    }

    public function count_posts()
    {

        $sql = "SELECT COUNT(*) AS total FROM posts";

        $statement = $this->run_query($sql);

        return $statement->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function count_posts_by_thread_id($thread_id)
    {
        $params = ['thread_id' => $thread_id];

        $sql = "SELECT COUNT(*) AS total FROM posts WHERE post_thread_id = :thread_id";

        $statement = $this->run_query($sql, $params);

        return $statement->fetch(PDO::FETCH_ASSOC)['total'];
    }

}