<?php

namespace Config;

class Database {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            try {
                self::$instance = new \PDO(
                    'mysql:host=db;dbname=course_catalog;charset=utf8',
                    'test_user',
                    'test_password'
                );
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}