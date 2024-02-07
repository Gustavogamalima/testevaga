<?php
// Verifica se a solicitação é um DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Verifica se o ID da pessoa foi enviado
    if (isset($_GET['id'])) {
        $idPessoa = $_GET['id'];

        // Aqui você deve incluir o código para remover a pessoa do banco de dados MySQL,
        // juntamente com todos os seus filhos (se necessário). Substitua isso com sua lógica
        // para excluir a pessoa e seus filhos com o ID fornecido

        // Exemplo de conexão com o banco de dados MySQL usando PDO
        $dsn = 'mysql:host=localhost;dbname=testevaga';
        $username = 'root';
        $password = '';
        
        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Excluir todos os filhos da pessoa
            $stmtFilhos = $pdo->prepare("DELETE FROM filho WHERE id_pessoa = :id_pessoa");
            $stmtFilhos->bindParam(':id_pessoa', $idPessoa, PDO::PARAM_INT);
            $stmtFilhos->execute();

            // Excluir a pessoa
            $stmtPessoa = $pdo->prepare("DELETE FROM pessoa WHERE id = :id");
            $stmtPessoa->bindParam(':id', $idPessoa, PDO::PARAM_INT);
            $stmtPessoa->execute();

            echo 'Pessoa e seus filhos removidos com sucesso.';
        } catch(PDOException $e) {
            echo 'Erro ao remover pessoa: ' . $e->getMessage();
        }
    } else {
        http_response_code(400); // Bad Request
        echo 'ID da pessoa não fornecido.';
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo 'Método não permitido. Apenas solicitações DELETE são suportadas.';
}
?>
