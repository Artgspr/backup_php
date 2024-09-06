<?php
include("cabecalho.php");
$conn = conecta();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_usuario']) && is_numeric($_POST['id_usuario'])) {
        $id = $_POST['id_usuario'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $telefone = $_POST['telefone'];
        $admin = $_POST['admin'];
        $excluido = $_POST['excluido'];

        $varSQL = "UPDATE usuario SET nome = :nome, email = :email, senha = :senha, telefone = :telefone, admin = :admin, excluido = :excluido WHERE id_usuario = :id";

        $update = $conn->prepare($varSQL);
        $update->bindParam(':nome', $nome);
        $update->bindParam(':email', $email);
        $update->bindParam(':senha', $senha);
        $update->bindParam(':telefone', $telefone);
        $update->bindParam(':admin', $admin);
        $update->bindParam(':excluido', $excluido);
        $update->bindParam(':id', $id);
        
        $update->execute();
        echo "<br><br> Dados do usuario atualizados com sucesso!";

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $arquivoRecebido = $_FILES['foto']['tmp_name'];
            $extensaoPadrao = 'jpg';
            $arquivoNovo = "imagens/u$id.$extensaoPadrao";

            if(move_uploaded_file($arquivoRecebido, $arquivoNovo ))
            echo"<br><br> Arquivo de foto foi recebido e atualizado com sucesso!";

        }

        echo "<br><br>";
        echo "<a href='usuario.php'><button type='button'>Voltar</button></a>";
    }
} elseif (isset($_GET['id_usuario']) && is_numeric($_GET['id_usuario'])) {
    $id = $_GET['id_usuario'];
    
    $varSQL = "SELECT * FROM usuario WHERE id_usuario = :id";
    $select = $conn->prepare($varSQL);
    $select->bindParam(':id', $id);
    $select->execute();
    $linha = $select->fetch();

    if ($linha) {
        $nome = $linha['nome'];
        $email = $linha['email'];
        $senha = $linha['senha'];
        $telefone = $linha['telefone'];
        $admin = $linha['admin'];
        $adminOpcao1 = ($admin == '0' ? 'selected' : '');
        $adminOpcao2 = ($admin == '1' ? 'selected' : '');
        $excluido = $linha['excluido'];
        $excluidoOpcao1 = ($excluido == '0' ? 'selected' : '');
        $excluidoOpcao2 = ($excluido == '1' ? 'selected' : '');

        echo "
        <form action='' method='post' enctype='multipart/form-data'>
            <label>Alterar Usuário</label><br><br>
            Nome: <input type='text' name='nome' value='$nome'><br><br>
            Email: <input type='email' name='email' value='$email'><br><br>
            Senha: <input type='password' name='senha' value='$senha'><br><br>
            Telefone: <input type ='text' name='telefone' value='$telefone'><br><br>
            Admin:
            <select name='admin'>
                <option $adminOpcao1 value='0'>Não</option>
                <option $adminOpcao2 value='1'>Sim</option>
            </select><br><br>
            Excluido:
            <select name='excluido'>
                <option $excluidoOpcao1 value='0'>Não</option>
                <option $excluidoOpcao2 value='1'>Sim</option>
            </select><br><br>
            <strong>Selecione uma foto:</strong>
            <input type='file' name='foto'><br><br>
            <input type='hidden' name='id_usuario' value='$id'>
            <input type='submit' value='Alterar Valores'>
            <a href='usuario.php'><button type='button'>Voltar</button></a>
        </form>";
    } else {
        echo "Usuário não encontrado.";
    }
}
?>
