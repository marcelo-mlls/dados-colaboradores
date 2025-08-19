<?php

    ini_set( 'display_errors', 1);
    ini_set('display_startup_errors', 1); // Exibe erros de startup
    ini_set('error_reporting', E_ALL); // Reporta todos os tipos de erro
    error_log("DEBUG: Script --->> Header.php iniciado em " . date("Y-m-d H:i:s") . "\n", 3, "/var/log/cron.log");

    require_once __DIR__ . '/../vendor/autoload.php';

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT,DELETE');
	header('Access-Control-Allow-Headers: Content-Type, Authorization');

	if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(204);
        exit;
    }

	header("Content-type: application/json; charset=utf-8");
	$data 		= json_decode(file_get_contents("php://input"), true);

	error_log("DEBUG: Script --->> Header.php finalizado em " . date("Y-m-d H:i:s") . "\n", 3, "/var/log/cron.log");

?>
