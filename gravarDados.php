<?php
// Verifica se a solicitação é um POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se os dados foram enviados
    $dadosJson = file_get_contents('php://input');
    if ($dadosJson !== false) {
        // Decodifica os dados JSON recebidos
        $dados = json_decode($dadosJson, true);

        // Aqui você deve incluir o código para gravar os dados no banco de dados MySQL.
        // Substitua isso com sua lógica para inserir os dados recebidos no banco de dados.

        // Exemplo de conexão com o banco de dados MySQL usando PDO
        $dsn = 'mysql:host=localhost;dbname=testevaga';
        $username = 'root';
        $password = '';
        
        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Inserir os dados das pessoas na tabela 'pessoa'
            foreach ($dados as $pessoa) {
                $stmtPessoa = $pdo->prepare("INSERT INTO pessoa (nome) VALUES (:nome)");
                $stmtPessoa->bindParam(':nome', $pessoa['nome'], PDO::PARAM_STR);
                $stmtPessoa->execute();
                $idPessoa = $pdo->lastInsertId(); // Obtém o ID da pessoa inserida

                // Inserir os dados dos filhos na tabela 'filho'
                foreach ($pessoa['filhos'] as $filho) {
                    $stmtFilho = $pdo->prepare("INSERT INTO filho (nome, id_pessoa) VALUES (:nome, :id_pessoa)");
                    $stmtFilho->bindParam(':nome', $filho['nome'], PDO::PARAM_STR);
                    $stmtFilho->bindParam(':id_pessoa', $idPessoa, PDO::PARAM_INT);
                    $stmtFilho->execute();
                }
            }

            echo 'Dados gravados com sucesso.';
        } catch(PDOException $e) {
            echo 'Erro ao gravar dados: ' . $e->getMessage();
        }
    } else {
        http_response_code(400); // Bad Request
        echo 'Dados não foram recebidos.';
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo 'Método não permitido. Apenas solicitações POST são suportadas.';
}


?>
