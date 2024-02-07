<?php
// Verifica se a solicitação é um DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Verifica se o ID do filho foi enviado
    if (isset($_GET['id'])) {
        $idFilho = $_GET['id'];

        // Aqui você deve incluir o código para remover o filho do banco de dados MySQL.
        // Substitua isso com sua lógica para excluir o filho com o ID fornecido.

        // Exemplo de conexão com o banco de dados MySQL usando PDO
        $dsn = 'mysql:host=localhost;dbname=testevaga';
        $username = 'root';
        $password = '';
        
        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Exemplo de uma consulta para excluir o filho com o ID fornecido
            $stmt = $pdo->prepare("DELETE FROM filho WHERE id = :id");
            $stmt->bindParam(':id', $idFilho, PDO::PARAM_INT);
            $stmt->execute();

            echo 'Filho removido com sucesso.';
        } catch(PDOException $e) {
            echo 'Erro ao remover filho: ' . $e->getMessage();
        }
    } else {
        http_response_code(400); // Bad Request
        echo 'ID do filho não fornecido.';
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo 'Método não permitido. Apenas solicitações DELETE são suportadas.';
}
?>
