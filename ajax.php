<?php
include 'database.php';

$conexao = new Conectar();

if (isset($_POST['gravar'])) {
    $json = $_POST['json'];
    new Gravar($conexao->conexao, $json);
    echo json_encode(['status' => 'success']);
}

if (isset($_GET['ler'])) {
    new Ler($conexao->conexao);
}

if (isset($_POST['adicionarFilho'])) {
    $pessoaIndex = $_POST['pessoaIndex'];
    $filhoNome = $_POST['filhoNome'];
    $conexao->conexao->query("INSERT INTO filho (pessoa_id, nome) VALUES ($pessoaIndex, '$filhoNome')");
}
// Remover pessoa pelo ID
if (isset($_POST['removerPessoa'])) {
    $id = $_POST['removerPessoa'];
    $conexao->conexao->query("DELETE FROM pessoa WHERE id = $id");
    $conexao->conexao->query("DELETE FROM filho WHERE pessoa_id = $id");
    echo json_encode(['status' => 'success']);
}

// Remover filho pelo ID
if (isset($_POST['removerFilho'])) {
    $id = $_POST['removerFilho'];
    $conexao->conexao->query("DELETE FROM filho WHERE id = $id");
    echo json_encode(['status' => 'success']);
}
?>