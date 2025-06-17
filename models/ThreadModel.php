<?php

// models/ThreadModel.php

class ThreadModel extends BaseModel
{
    public function get_thread($thread_id)
    {
        $params = [
            'thread_id' => $thread_id
        ];

        $sql = "SELECT * FROM threads WHERE thread_id = :thread_id";

        $statement = $this->run_query($sql, $params);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function get_threads(array $options = [], bool $count_only = false)
    {
        $where = [];
        $params = [];

        if (!empty($options['category_id'])) {
            $where[] = 'threads.thread_category = :category_id';
            $params[':category_id'] = $options['category_id'];
        }
        if (!empty($options['search'])) {
            $where[] = 'threads.thread_title LIKE :search';
            $params[':search'] = '%' . $options['search'] . '%';
        }
        $where_clause = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

        if ($count_only) {
            $sql = "SELECT COUNT(*) AS total FROM threads" . $where_clause;
        } else {
            $sql = "
                SELECT 
                    threads.*,
                    (SELECT COUNT(*) FROM posts WHERE posts.post_thread_id = threads.thread_id) AS post_count
                FROM threads
                $where_clause
                ORDER BY threads.thread_pin DESC, threads.thread_last_post_timestamp DESC
            ";
            if (isset($options['limit']) && isset($options['offset'])) {
                $sql .= " LIMIT :limit OFFSET :offset";
                $params[':limit'] = (int) $options['limit'];
                $params[':offset'] = (int) $options['offset'];
            }
        }
        $statement = $this->run_query($sql, $params);

        return $count_only ? ($statement->fetch(PDO::FETCH_ASSOC)['total'] ?? 0) : $statement->fetchAll(PDO::FETCH_ASSOC);

    }

    public function update_thread_row($columns, $conditions)
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
            $sql = "UPDATE threads SET " . implode(', ', $fields) . " WHERE " . implode(' AND ', $where_clauses);

            $statement = $this->run_query($sql, $params);

            return ($statement->rowCount() > 0);
        }

    }

    public function delete_thread($thread_id)
    {
        $params = [
            'thread_id' => $thread_id,
        ];

        $sql = "DELETE FROM threads WHERE thread_id = :thread_id";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);

    }

    public function create_thread($data)
    {
        $params = [
            ':thread_title' => $data['thread_title'],
            ':thread_author' => $data['thread_author'],
            ':thread_pin' => $data['thread_pin'],
            ':thread_lock' => $data['thread_lock'],
            ':thread_category' => $data['thread_category']
        ];

        $sql = "
            INSERT INTO threads (
                thread_title, 
                thread_author, 
                thread_pin, 
                thread_lock, 
                thread_category
            ) VALUES (
                :thread_title, 
                :thread_author, 
                :thread_pin, 
                :thread_lock, 
                :thread_category
            )
        ";

        $this->run_query($sql, $params);

        return $this->db->lastInsertId();
        
    }

    public function update_thread_last_post($data)
    {
        $params = [
            ':thread_id' => $data['thread_id'],
            ':thread_last_post_author' => $data['thread_last_post_author'],
            ':thread_last_post_timestamp' => $data['thread_last_post_timestamp'],
            ':thread_last_post_id' => $data['thread_last_post_id']
        ];

        $sql = "
            UPDATE threads 
            SET thread_last_post_timestamp = :thread_last_post_timestamp, 
                thread_last_post_author = :thread_last_post_author,
                thread_last_post_id = :thread_last_post_id
            WHERE thread_id = :thread_id
        ";

        $statement = $this->run_query($sql, $params);

        return ($statement->rowCount() > 0);
        
    }
}
