<?php

    ini_set('display_errors', 1); // Exibe erros no stdout
    ini_set('display_startup_errors', 1); // Exibe erros de startup
    ini_set('error_reporting', E_ALL); // Reporta todos os tipos de erro
    error_log("DEBUG: Script ---> enviarCenso.php iniciado em " . date("Y-m-d H:i:s") . "\n", 3, "/var/log/cron.log");

    require_once(__DIR__ . "/../includes/config.php");
    require_once(__DIR__ . "/../includes/header.php");
    require_once(__DIR__ . "/../includes/oracle.php");
    require_once(__DIR__ . "/../includes/functions.php");

    echo "\n Iniciando Processo de Envio de Censo... \n";

    echo "\n Realizando o processo de autenticação... \n";

    $token = api_login();

    echo "\n Autenticado ! \n";

    if ($token["error"] != null) {
        echo "\n Mensagem de retorno código: " . $token["http_code"];
        echo "\n Erro ao autenticar: " . $token["error"];
    } else {
        echo "\n Mensagem de retorno código: " . $token["http_code"];
        echo "\n Token de autenticação: " . $token["token"];
        echo "\n Autenticação realizada com sucesso! \n";
    }

    echo "\n Realizar a busca dos dados do censo no banco de dados Oracle";

    $colunas    = " * ";
    $tabela     = " UN_DADOS_COLABORADORES ";
    $filtro     = " 1 = 1 ";
    $sql        = "SELECT $colunas FROM $tabela WHERE $filtro";
    echo "\n SQL para busca dos dados a serem enviados: $sql";
    try {
        $stidDados = oci_parse($oracleDB, $sql);
        if (!$stidDados) {
            $e = oci_error($oracleDB);
            throw new Exception("Erro ao preparar a consulta: " . $e['message']);
        }
        
        echo "\n Executar a consulta";
        $result = oci_execute($stidDados);
        if (!$result) {
            $e = oci_error($stidDados);
            throw new Exception("Erro ao executar a consulta: " . $e['message']);
        }
        
        echo "\n Obter todos os resultados de uma vez";
        $results = [];
        $numRows = oci_fetch_all($stidDados, $results, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
        
        if ($numRows === 0) {
            throw new Exception("Nenhum dado encontrado para envio.");
        }
        
        echo "\n Consulta executada com sucesso. $numRows registros encontrados.";
        echo "\n\n Iniciando processamento dos registros:\n";
        
        $contador = 0;
        foreach ($results as $row) {
            $contador++;
            echo "\n Registro #$contador:";

            echo "\n Estruturando o cabeçalho do envio...";
            $header = [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token["token"]
            ];
            
            echo  "Processar cada coluna do registro \n";
            foreach ($row as $coluna => $valor) {
                echo "\n - $coluna: " . (is_null($valor) ? 'NULL' : $valor) . "\n";
                
                $body[$coluna] = $valor;
            }
            
            $jsonBody = json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            echo "\n Objeto JSON a ser enviado: $jsonBody \n";

            echo "\n Enviando dados para a API... \n";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $config["rotas"]["enviar"]);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonBody);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            echo "\n Obtendo dados da requisição";
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            var_dump($response);
            var_dump($httpCode);
            var_dump($curlError);

            echo "\n ------------------------------------";
        }
        
        echo "\n\n Processamento concluído. Total de registros processados: $contador";
        
    } catch (Exception $e) {
        echo "\n ERRO: " . $e->getMessage();
        // Finaliza a execução em caso de erro
        exit;
    }
    
    // Liberar recursos
    oci_free_statement($stidDados);
?>