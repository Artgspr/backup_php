<?php
include("cabecalho.php");
echo "
<form method='post' action='' enctype='multipart/form-data'>
<label>Adicionar Usuário</label> <br> <br>

<label for='nome'>Nome:</label>
<input type='text' name='nome'>
<br><br>

<label for='email'>Email:</label>
<input type='email' name='email'>
<br><br>

<label for='senha'>Senha:</label>
<input type='password' name='senha'>
<br><br>

<label for='telefone'>Telefone:</label>
<input type='text' name='telefone'>
<br><br>

<label for='admin'>Admin:</label>
<select name='admin'>
    <option value='0'>Não</option> Usuario Comum
    <option value='1'>Sim</option> Administrador
</select>
<br><br>

<label for='excluido'>Excluido:</label>
<select name='excluido'>
    <option value='0'>Não</option> Falso
    <option value='1'>Sim</option> Verdadeiro
</select>
<br><br>

<strong>Selecione uma foto:</strong> 
<input type='file' name='foto'> <br><br>

<button type='submit'>Enviar dados</button>

<br><br>
<a href='usuario.php'><button type='button'>Voltar</button></a>
</form>
";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = conecta();

    $varSQL = "INSERT INTO usuario (nome, email, senha, telefone, admin, excluido)
                VALUES (:nome, :email, :senha, :telefone, :admin, :excluido)";

    $insert = $conn->prepare($varSQL);
    $insert->bindParam(':nome', $_POST['nome']);
    $insert->bindParam(':email', $_POST['email']);
    $insert->bindParam(':senha', $_POST['senha']);
    $insert->bindParam(':telefone', $_POST['telefone']);
    $insert->bindParam(':admin', $_POST['admin']);
    $insert->bindParam(':excluido', $_POST['excluido']);
    $insert->execute();

    if ($_FILES && isset($_FILES['foto'])) {
        $id = $conn->lastInsertId();
        $arquivoRecebido = $_FILES['foto']['tmp_name'];
        $extensaoPadrao = 'jpg';
        $arquivoNovo = "imagens/u$id.$extensaoPadrao";

        if (move_uploaded_file($arquivoRecebido, $arquivoNovo)) {
            echo "<br><br>Arquivo de foto foi recebido com sucesso!";
        } else {
            echo "<br><br>Nenhuma foto foi enviada";
            echo "<br><br>Uma foto generica foi adicionada";
            
        }
    }
}
?>
