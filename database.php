<?php
class Conectar {
    private $dsn = 'mysql:host=localhost;dbname=testevaga';
    private $username = 'root';
    private $password = '';
    protected $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Erro de conexão: ' . $e->getMessage();
        }
    }
}


class GravarDados extends Conectar {
    public function gravarPessoas($dados) {
        try {
            // Iniciar uma transação para garantir consistência dos dados
            $this->pdo->beginTransaction();
    
            // Array para armazenar os IDs das pessoas já existentes no banco de dados
            $existingPersonIds = $this->getExistingPersonIds();
    
            // Percorrer os dados recebidos para gravar/atualizar pessoas e filhos
            foreach ($dados as $pessoa) {
                // Verificar se a pessoa já existe no banco de dados
                if (in_array($pessoa['id'], $existingPersonIds)) {
                    // Se a pessoa já existe, atualizar seus dados
                    $this->updatePerson($pessoa);
                } else {
                    // Se a pessoa não existe, inseri-la
                    $this->insertPerson($pessoa);
                }
    
                // Recuperar o ID da pessoa
                $personId = $pessoa['id'];
    
                // Excluir todos os filhos existentes da pessoa
                $this->deleteChildren($personId);
    
                // Inserir os filhos da pessoa
                if (isset($pessoa['filhos']) && is_array($pessoa['filhos'])) {
                    foreach ($pessoa['filhos'] as $filho) {
                        // Atribuir o ID da pessoa pai ao filho
                        $filho['id_pessoa'] = $personId;
    
                        // Inserir o novo filho
                        $this->insertChild($filho);
                    }
                }
            }
    
            // Confirmar a transação
            $this->pdo->commit();
    
            return 'Dados gravados com sucesso.';
        } catch(PDOException $e) {
            // Reverter a transação em caso de erro
            $this->pdo->rollBack();
            return 'Erro ao gravar dados: ' . $e->getMessage();
        }
    }
    
    public function updatePerson($pessoa) {
        $stmt = $this->pdo->prepare("UPDATE pessoa SET nome = :nome WHERE id = :id");
        $stmt->bindParam(':id', $pessoa['id'], PDO::PARAM_INT);
        $stmt->bindParam(':nome', $pessoa['nome'], PDO::PARAM_STR);
        $stmt->execute();
    }

    public function deleteChildren($personId) {
        $stmt = $this->pdo->prepare("DELETE FROM filho WHERE id_pessoa = :id_pessoa");
        $stmt->bindParam(':id_pessoa', $personId, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    private function getExistingPersonIds() {
        $stmt = $this->pdo->query("SELECT id FROM pessoa");
        $existingPersonIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $existingPersonIds;
    }

    private function insertPerson($pessoa) {
        $stmt = $this->pdo->prepare("INSERT INTO pessoa (id, nome) VALUES (:id, :nome)");
        $stmt->bindParam(':id', $pessoa['id'], PDO::PARAM_INT);
        $stmt->bindParam(':nome', $pessoa['nome'], PDO::PARAM_STR);
        $stmt->execute();
    }

    private function insertChild($filho) {
        $stmt = $this->pdo->prepare("INSERT INTO filho (id, nome, id_pessoa) VALUES (:id, :nome, :id_pessoa)");
        $stmt->bindParam(':id', $filho['id'], PDO::PARAM_INT);
        $stmt->bindParam(':nome', $filho['nome'], PDO::PARAM_STR);
        $stmt->bindParam(':id_pessoa', $filho['id_pessoa'], PDO::PARAM_INT);
        $stmt->execute();
    }
}



class Ler extends Conectar {
    public function lerPessoas() {
        try {
            // Consulta para recuperar os dados das pessoas e seus filhos
            $stmtPessoas = $this->pdo->query("SELECT * FROM pessoa");
            $pessoas = $stmtPessoas->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pessoas as &$pessoa) {
                $stmtFilhos = $this->pdo->prepare("SELECT * FROM filho WHERE id_pessoa = :id_pessoa");
                $stmtFilhos->bindParam(':id_pessoa', $pessoa['id'], PDO::PARAM_INT);
                $stmtFilhos->execute();
                $pessoa['filhos'] = $stmtFilhos->fetchAll(PDO::FETCH_ASSOC);
            }

            return json_encode($pessoas);
        } catch(PDOException $e) {
            return 'Erro ao ler dados: ' . $e->getMessage();
        }
    }
}

class RemoverPessoa extends Conectar {
    public function removerPessoa($idPessoa) {
        try {
            // Excluir todos os filhos da pessoa
            $stmtFilhos = $this->pdo->prepare("DELETE FROM filho WHERE id_pessoa = :id_pessoa");
            $stmtFilhos->bindParam(':id_pessoa', $idPessoa, PDO::PARAM_INT);
            $stmtFilhos->execute();

            // Excluir a pessoa
            $stmtPessoa = $this->pdo->prepare("DELETE FROM pessoa WHERE id = :id");
            $stmtPessoa->bindParam(':id', $idPessoa, PDO::PARAM_INT);
            $stmtPessoa->execute();

            return 'Pessoa e seus filhos removidos com sucesso.';
        } catch(PDOException $e) {
            return 'Erro ao remover pessoa: ' . $e->getMessage();
        }
    }
}

class RemoverFilho extends Conectar {
    public function removerFilho($idFilho) {
        try {
            // Excluir o filho
            $stmt = $this->pdo->prepare("DELETE FROM filho WHERE id = :id");
            $stmt->bindParam(':id', $idFilho, PDO::PARAM_INT);
            $stmt->execute();

            return 'Filho removido com sucesso.';
        } catch(PDOException $e) {
            return 'Erro ao remover filho: ' . $e->getMessage();
        }
    }
}


class CriaBD {
    public function __construct($conexao) {
        $sql = "CREATE TABLE IF NOT EXISTS pessoa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL
        )";
        $conexao->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS filho (
            id INT AUTO_INCREMENT PRIMARY KEY,
            pessoa_id INT,
            nome VARCHAR(255) NOT NULL,
            FOREIGN KEY (pessoa_id) REFERENCES pessoa(id)
        )";
        $conexao->query($sql);
    }
}

