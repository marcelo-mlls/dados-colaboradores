<?php

    function api_login(){
        global $config;

        $urlAuth = $config['rotas']['autenticacao'];
        echo "\n Solicitando Autenticação para: " . $urlAuth . "\n";

        $objAuth = [
            "username" => getenv('API_USER'),
            "password" => getenv('API_PASS')
        ];
        $jsonAuth = json_encode($objAuth);
        echo "\n Objeto envaido para autenticação: $jsonAuth \n";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $urlAuth);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonAuth);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $retorno = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        $errno = curl_errno($curl);

        $response = [];

        if ($httpCode == 400) {
            // Se a resposta for plain text (não JSON), trate o erro como string.
            if (strpos($retorno, 'Usuário ou senha inválido.') !== false) {
                $response["http_code"] = $httpCode;
                $response["token"] = null;
                $response["error"] = "Erro ao autenticar: Usuário ou senha inválidos!";
            } else {
                // Caso contrário, trate como erro genérico
                $response["http_code"] = $httpCode;
                $response["token"] = null;
                $response["error"] = "Erro desconhecido: " . $retorno;
            }
        } else if ($httpCode == 401) {
            $response["http_code"] = $httpCode;
            $response["token"] = null;
            $response["error"] = "Erro ao autenticar: Usuário não autorizado a realizar a solicitação!";
        } else if ($httpCode == 200) {
            // Verifica se o retorno é um JSON válido
            $data = json_decode($retorno, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $response["http_code"] = $httpCode;
                $response["token"] = $data['token'] ?? null;  // Se o token estiver presente no JSON
                $response["error"] = null;
            } else {
                $response["http_code"] = $httpCode;
                $response["token"] = null;
                $response["error"] = "Erro inesperado no formato de resposta.";
            }
        } else {
            $response["http_code"] = $httpCode;
            $response["token"] = null;
            $response["error"] = "Erro inesperado: " . $retorno;
        }

        curl_close($curl);
        return $response;
    }






?>