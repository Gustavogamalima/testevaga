<?php
require 'database.php';

// Verifica se a solicitação é um DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Verifica se o ID da pessoa foi enviado
    if (isset($_GET['id'])) {
        $idPessoa = $_GET['id'];
        
        $removerPessoa = new RemoverPessoa();
        echo $removerPessoa->removerPessoa($idPessoa);
    } else {
        http_response_code(400); // Bad Request
        echo 'ID da pessoa não fornecido.';
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo 'Método não permitido. Apenas solicitações DELETE são suportadas.';
}
?>
