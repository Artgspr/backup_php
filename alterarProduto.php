<?php
include("cabecalho.php");
$conn = conecta();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_produto']) && is_numeric($_POST['id_produto'])) {
    
        $id = $_POST['id_produto'];
        $nome = $_POST['nome'];
        $descricao = $_POST['descricao'];
        $valor_unitario = $_POST['valor_unitario'];
        $excluido = isset($_POST['excluido']) ? 1 : 0;
        $data_exclusao = $_POST['data_exclusao'];
        $data_exclusao = !empty($data_exclusao) ? $data_exclusao : null;
        $qtde_estoque = $_POST['qtde_estoque'];
        

        $varSQL = "UPDATE produto SET nome = :nome, descricao = :descricao, valor_unitario = :valor_unitario, excluido = :false, data_exclusao = :data_exclusao, qtde_estoque = :qtde_estoque WHERE id_produto = :id";
        $update = $conn->prepare($varSQL);
        $update->bindParam(':nome', $nome);
        $update->bindParam(':descricao', $descricao);
        $update->bindParam(':valor_unitario', $valor_unitario); 
        $update->bindParam(':false', $excluido);
        $update->bindParam(':data_exclusao', $data_exclusao);
        $update->bindParam(':qtde_estoque', $qtde_estoque);
        $update->bindParam(':id', $id);

    
        $update->execute();
        echo "<br><br> Dados do produto atualizados com sucesso!";
      
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $arquivoRecebido = $_FILES['foto']['tmp_name'];
            $extensaoPadrao = 'jpg';
            $arquivoNovo = "imagens/p$id.$extensaoPadrao";

            if(move_uploaded_file($arquivoRecebido, $arquivoNovo ))
            echo"<br><br> Arquivo de foto foi recebido e atualizado com sucesso!";

        }

        echo "<br><br>";
        echo "<a href='produto.php'><button type='button'>Voltar</button></a>";
    }
    }

elseif (isset($_GET['id_produto']) && is_numeric($_GET['id_produto'])) {
    $id = $_GET['id_produto'];

    $varSQL = "SELECT * FROM produto WHERE id_produto = :id";
    $select = $conn->prepare($varSQL);
    $select->bindParam(':id', $id);
    $select->execute();
    $linha = $select->fetch();

    if ($linha) {
        $id_produto = ($linha['id_produto']);
        $nome = ($linha['nome']);
        $descricao = ($linha['descricao']);
        $valor_unitario = ($linha['valor_unitario']);
        $excluido = ($linha['excluido']);
        $data_exclusao = ($linha['data_exclusao']);
        $qtde_estoque = ($linha['qtde_estoque']);

        echo "
        <form action='' method='post' enctype='multipart/form-data'>
            <label>Alterar Produto</label><br><br>

            <label for='nome'>Nome:</label>
            <input type='text' name='nome' value='$nome'><br><br>

            <label for='descricao'>Descrição:</label>
            <input type='text' name='descricao' value='$descricao'><br><br>

            <label for='valor_unitario'>Valor Unitário:</label>
            <input type='number' step='0.01' name='valor_unitario' value='$valor_unitario'><br><br>

            <label for='excluido'>Excluído:</label>
            <input type='checkbox' id='excluido' name='excluido' value='1' ".($excluido ? 'checked' : '')."><br><br>

            <label for='data_exclusao'>Data da Exclusão:</label>
            <input type='date' id='data_exclusao' name='data_exclusao' value='$data_exclusao'><br><br>

            <label for='qtde_estoque'>Quantidade em Estoque:</label>
            <input type='number' name='qtde_estoque' value='$qtde_estoque'><br><br>

            <strong>Selecione uma foto:</strong>
            <input type='file' name='foto'><br><br>

            <input type='hidden' name='id_produto' value='$id'>
            <button type='submit'>Alterar dados</button>
            <a href='produto.php'><button type='button'>Voltar</button></a>
        </form>
        ";
    } else {
        echo "Produto não encontrado.";
    }
} 

?>
