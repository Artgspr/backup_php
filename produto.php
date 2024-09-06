<?php

include("cabecalho.php");
$conn = conecta();

if ($_POST){
    $varPesquisa = '%'.$_POST['varPesquisa'].'%';
} else{
    $varPesquisa = "%%";
}

$varSQL = "SELECT * FROM produto 
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
    </form>
    <br>";

echo "<br><table border='1' cellpadding='10' cellspacing='0'; border-collapse:collapse;>
        <tr>
            <th style='background-color:#77DD77; font-size:18px;'>Imagem</th>
            <th style='background-color:#77DD77; font-size:18px;'> Id Produto </th>
            <th style='background-color:#77DD77; font-size:18px;'>Nome</th>
            <th style='background-color:#77DD77; font-size:18px;'>Descrição</th>
            <th style='background-color:#77DD77; font-size:18px;'>Valor Unitário</th>
            <th style='background-color:#77DD77; font-size:18px;'>Excluído</th>
            <th style='background-color:#77DD77; font-size:18px;'>Data de Exclusão</th>
            <th style='background-color:#77DD77; font-size:18px;'>Quantidade de Estoque</th>
            <th style='background-color:#77DD77; font-size:18px;'colspan='2'>Opções</th>
        </tr>";

while ($linha = $select->fetch()) {
    echo "<tr>";

    $varFoto = "imagens/p" . $linha['id_produto'] . ".jpg";
    echo "<td>";
    if (file_exists($varFoto)) {
        echo "<img src='$varFoto' width='80'>";
    } else {
        echo "<img src='imagens/foto_p.jpg' width='80'>";
    }
    echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["id_produto"];
	echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["nome"];
	echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["descricao"];
	echo "</td>";


    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["valor_unitario"];
	echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo ($linha["excluido"] ? 'Sim' : 'Não');
	echo "</td>";

	echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["data_exclusao"];
	echo "</td>";

    echo "<td style='text-align:center; font-size:18px;'>";
	echo $linha["qtde_estoque"];
	echo "</td>";

    echo "  <td>
                <a href='alterarProduto.php?id_produto=" . $linha['id_produto'] . "'><button>Alterar</button></a>
            </td>
            <td>
                <a href='excluirProduto.php?id_produto=" . $linha['id_produto'] . "'><button>Excluir</button></a>
            </td>";

    echo "</tr>";     
}

echo "</table>";

echo "<br>";
echo "<a href='adicionarProduto.php'><button type='button'>Adicionar</button></a>";