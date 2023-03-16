<?php

// Start server: php -S localhost:8000 -t tests/server

$uri = $_SERVER["REQUEST_URI"];

// html from https://getcomposer.org
if ($uri === '/') {
    echo file_get_contents(__DIR__ . '/../data/composer/index.html');
    return;
}

// Parse json and return it
if ($uri === '/json') {
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    echo json_encode($data, JSON_PRETTY_PRINT);
    return;
}
