<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $conexao = new Conectar();
    $gravar = new Gravar($conexao->conexao, $json);
    echo "Dados gravados com sucesso.";
} else {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "Método não permitido.";
}
?>
