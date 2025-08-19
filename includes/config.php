<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);
error_log("DEBUG: Script ---> Config.php iniciado em " . date("Y-m-d H:i:s") . "\n", 3, "/var/log/cron.log");

$config = [
    "rotas" => [
        "autenticacao"      => getenv('API_URL') . "auth/login",
        "refresh"           => getenv('API_URL') . "auth/refresh",
        "enviar"            => getenv('API_URL') . "enviar"
    ] ,
    "oracle" => [
        "host"          => getenv('DB_HOST'),
        "port"          => getenv('DB_PORT'),
        "service_name"  => getenv('DB_SERVICE'),
        "username"      => getenv('DB_USERNAME'),
        "password"      => getenv('DB_PASSWORD'),
        "charset"       => getenv('DB_CHARSET')
    ]
];

error_log("DEBUG: Script --->> Config.php finalizado em " . date("Y-m-d H:i:s") . "\n", 3, "/var/log/cron.log");
?>