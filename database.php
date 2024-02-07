<?php
class Conectar {
    private $host = "localhost";
    private $usuario = "root";
    private $senha = "";
    private $banco = "testevaga";
    public $conexao;

    public function __construct() {
        $this->conexao = new mysqli($this->host, $this->usuario, $this->senha, $this->banco);
        if ($this->conexao->connect_error) {
            die("Erro na conexão: " . $this->conexao->connect_error);
        } else {
            // echo "Conexão bem-sucedida!";
        }
    }
}

class Gravar {

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
class Ler {

}



class RemoverPessoa {
}

class RemoverFilho {
 
}
