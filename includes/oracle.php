<?php

    ini_set('display_errors', 1); // Exibe erros no stdout
    ini_set('display_startup_errors', 1); // Exibe erros de startup
    ini_set('error_reporting', E_ALL); // Reporta todos os tipos de erro
    error_log("DEBUG: Script ---> Oracle.php iniciado em " . date("Y-m-d H:i:s") . "\n", 3, "/var/log/cron.log");

	include(__DIR__ . "/config.php");

    $oracleDB 	= oci_new_connect($config["oracle"]["username"], $config["oracle"]["password"], $config["oracle"]["host"] . ":" . $config["oracle"]["port"] . '/' . $config["oracle"]["service_name"], $config["oracle"]["charset"]);
    if (!$oracleDB) {
        $e  	= oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        exit();
    }	
    $stid1 		= oci_parse($oracleDB, "ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD hh24:mi:ss'");
    oci_execute($stid1);
    error_log("DEBUG: Script ---> Oracle.php finalizado em " . date("Y-m-d H:i:s") . "\n", 3, "/var/log/cron.log");
?>