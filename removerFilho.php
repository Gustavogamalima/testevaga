<?php
// Conexão com o banco de dados (substitua pelos seus dados de conexão)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testevaga";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Verifica se o ID do filho foi enviado via método DELETE
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    // Obtém o ID do filho a ser removido
    $id_filho = $_GET['id'];

    // Query para remover o filho do banco de dados
    $sql = "DELETE FROM filho WHERE id = $id_filho";

    if ($conn->query($sql) === TRUE) {
        // Retorna uma resposta de sucesso ao cliente
        echo "Filho removido com sucesso.";
    } else {
        // Retorna uma mensagem de erro ao cliente, se houver algum problema com a consulta SQL
        echo "Erro ao remover filho: " . $conn->error;
    }
} else {
    // Se o método da requisição não for DELETE, retorna um erro
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Método não permitido.";
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
