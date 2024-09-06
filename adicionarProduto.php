<?php

include("cabecalho.php");
$conn = conecta();

echo "
<form method='post' action='' enctype='multipart/form-data'>
    <label>Adicionar Produto</label><br><br>

    <label for='nome'>Nome:</label>
    <input type='text' name='nome' required><br><br>

    <label for='descricao'>Descrição:</label>
    <input type='text' name='descricao' required><br><br>

    <label for='valor_unitario'>Valor Unitário:</label>
    <input type='number' step='0.01' name='valor_unitario' required><br><br>

    <label for='excluido'>Excluído:</label>
    <input type='checkbox' id='excluido' name='excluido'><br><br>

    <label for='data_exclusao'>Data da Exclusão:</label>
    <input type='date' id='data_exclusao' name='data_exclusao'><br><br>

    <label for='qtde_estoque'>Quantidade em Estoque:</label>
    <input type='number' name='qtde_estoque' required><br><br>

    <strong>Selecione uma foto:</strong> 
    <input type='file' name='foto'><br><br>

    <button type='submit'>Enviar dados</button>

    <br><br>
    <a href='produto.php'><button type='button'>Voltar</button></a>
</form>
";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $valor_unitario = (float) $_POST['valor_unitario'];
    $excluido = isset($_POST['excluido']) ? 1 : 0;
    $data_exclusao = !empty($_POST['data_exclusao']) ? $_POST['data_exclusao'] : null;
    $qtde_estoque = (int) $_POST['qtde_estoque'];

    $varSQL = "INSERT INTO produto (nome, descricao, valor_unitario, excluido, data_exclusao, qtde_estoque)
               VALUES (:nome, :descricao, :valor_unitario, :false, :data_exclusao, :qtde_estoque)";
    $insert = $conn->prepare($varSQL);
    $insert->bindParam(':nome', $nome);
    $insert->bindParam(':descricao', $descricao);
    $insert->bindParam(':valor_unitario', $valor_unitario);
    $insert->bindParam(':false', $excluido);
    $insert->bindParam(':data_exclusao', $data_exclusao);
    $insert->bindParam(':qtde_estoque', $qtde_estoque);
    $insert->execute();

    if ($_FILES && isset($_FILES['foto'])) {
        $id = $conn->lastInsertId();
        $arquivoRecebido = $_FILES['foto']['tmp_name'];
        $extensaoPadrao = 'jpg';
        $arquivoNovo = "imagens/p$id.$extensaoPadrao";

        if (move_uploaded_file($arquivoRecebido, $arquivoNovo)) {
            echo "<br><br>Arquivo de foto foi recebido com sucesso!";
        } else {
            echo "<br><br>Nenhuma foto foi enviada";
            echo"<br><br>Uma foto generica foi adicionada";
        }
    }
}
?>
