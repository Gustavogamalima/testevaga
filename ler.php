<?php
require 'database.php';

// Verifica se a solicitação é um GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $ler = new Ler();
    echo $ler->lerPessoas();
} else {
    http_response_code(405); // Method Not Allowed
    echo 'Método não permitido. Apenas solicitações GET são suportadas.';
}
?>
