<?php
require 'database.php';

// Verifica se a solicitação é um POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os dados foram enviados
    $dadosJson = file_get_contents('php://input');
    if ($dadosJson !== false) {
        // Decodifica os dados JSON recebidos
        $dados = json_decode($dadosJson, true);
        
        $gravarDados = new GravarDados();
        echo $gravarDados->gravarPessoas($dados);
    } else {
        http_response_code(400); // Bad Request
        echo 'Dados não foram recebidos.';
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo 'Método não permitido. Apenas solicitações POST são suportadas.';
}
?>
