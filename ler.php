<?php
// Verifica se a solicitação é um GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Aqui você deve incluir o código para ler os dados do banco de dados MySQL.
    // Substitua isso com sua lógica para recuperar os dados das tabelas 'pessoa' e 'filho'.

    // Exemplo de conexão com o banco de dados MySQL usando PDO
    $dsn = 'mysql:host=localhost;dbname=testevaga';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta para recuperar os dados das pessoas e seus filhos
        $stmtPessoas = $pdo->query("SELECT * FROM pessoa");
        $pessoas = $stmtPessoas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pessoas as &$pessoa) {
            $stmtFilhos = $pdo->prepare("SELECT * FROM filho WHERE id_pessoa = :id_pessoa");
            $stmtFilhos->bindParam(':id_pessoa', $pessoa['id'], PDO::PARAM_INT);
            $stmtFilhos->execute();
            $pessoa['filhos'] = $stmtFilhos->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($pessoas);
    } catch(PDOException $e) {
        echo 'Erro ao ler dados: ' . $e->getMessage();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo 'Método não permitido. Apenas solicitações GET são suportadas.';
}
?>
