<?php

// core/Redis.php

class RedisClient {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new Redis();
            self::$instance->connect('127.0.0.1', 6379);
        }
        return self::$instance;
    }
}