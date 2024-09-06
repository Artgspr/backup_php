<?php

include("cabecalho.php");
$conn = conecta();

if ($_POST){
    $varPesquisa = '%'.$_POST['varPesquisa'].'%';
} else{
    $varPesquisa = "%%";
}

$varSQL = "SELECT * FROM usuario 
        WHERE nome LIKE :varPesquisa 
        AND NOT excluido 
        ORDER BY nome"; 

$select = $conn->prepare($varSQL);
$select->bindParam(':varPesquisa',$varPesquisa);
$select->execute();

echo "
    <form method='post' action=''>
        <input type='text' name='varPesquisa' placeholder='Buscar por nome' value='" . htmlspecialchars($_POST['varPesquisa'] ?? '') . "'>
        <input type='submit' value='Buscar'>
    </form> <br>
    <br>";

echo "<table border='1' cellpadding='10' cellspacing='0'; border-collapse:collapse;>
        	<tr>
				<th style='background-color:#77DD77; font-size:18px;'>Imagem </th>
                <th style='background-color:#77DD77; font-size:18px;'> Id Usuario </th>
            	<th style='background-color:#77DD77; font-size:18px;'>Nome</th>
            	<th style='background-color:#77DD77; font-size:18px;'>Email</th>
            	<th style='background-color:#77DD77; font-size:18px;'>Senha</th>
				<th style='background-color:#77DD77; font-size:18px;'>Telefone</th>
            	<th style='background-color:#77DD77; font-size:18px;'>Admin</th>
                <th style='background-color:#77DD77; font-size:18px;'>Excluído</th>
                <th style='background-color:#77DD77; font-size:18px;'colspan='2'>Opções</th>
        	</tr>" ;

    while ($linha = $select->fetch()) {
        echo "<tr>";
            
                
        $varFoto = "imagens/u" . $linha['id_usuario'] . ".jpg";
        echo "<td>";
        if (file_exists($varFoto)) {
        echo "<img src='$varFoto' width='80'>";
        } 
        else {
        echo "<img src='imagens/foto_u.jpg' width='80'>";
            }

	echo "</td>";

   
    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["id_usuario"];
	echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["nome"];
	echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["email"];
	echo "</td>";


    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["senha"];
	echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["telefone"];
	echo "</td>";

	echo "<td style='text-align:center; font-size:18px;'>";
	echo ($linha["admin"] ?'Sim' : 'Não');
	echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo ($linha["excluido"] ?'Sim' : 'Não');
	echo "</td>";
   
    echo "<td>
            <a href='alterarUsuario.php?id_usuario=" . $linha['id_usuario'] . "'><button>Alterar</button></a>
        </td>
        <td>
            <a href='excluirUsuario.php?id_usuario=" . $linha['id_usuario'] . "'><button>Excluir</button></a>
          </td>";

    echo "</tr>";     
 }

echo "</table>";

echo "<br>";
echo "<a href='adicionarUsuario.php'><button type='button'>Adicionar</button></a>"


?>
