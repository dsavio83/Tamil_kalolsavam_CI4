<?php

if (! function_exists('connectionLink')) {
    function connectionLink()
    {
        $db = config('Database');

        $hostname = $db->default['hostname'];
        $username = $db->default['username'];
        $password = $db->default['password'];
        $database = $db->default['database'];
        $port     = (int) ($db->default['port'] ?? 3306);

        $connection = mysqli_connect(
            $hostname,
            $username,
            $password,
            $database,
            $port
        );

        if (!$connection) {
            throw new \RuntimeException(
                'Database connection failed: ' . mysqli_connect_error()
            );
        }

        mysqli_set_charset($connection, 'utf8mb4');

        return $connection;
    }
}
