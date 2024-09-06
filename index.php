<?php
//cabeçalho
include("cabecalho.php");
$conn = conecta();

$varSQL = "SELECT id_produto, nome, descricao, valor_unitario, qtde_estoque FROM produto WHERE excluido = FALSE";
$select = $conn->prepare($varSQL);
$select->execute();

echo "<table border='1' width='100%'>";
$card = 0;

while ($linha = $select->fetch()) {
    $id_produto = $linha['id_produto'];
    $nome = $linha['nome'];
    $descricao = $linha['descricao'];
    $valor_unitario = number_format($linha['valor_unitario'], 2, ',', '.');
    $qtde_estoque = $linha['qtde_estoque'];

    $foto = "imagens/p" . $id_produto . ".jpg";
    $htmlFoto = (file_exists($foto) ? "<img src='$foto' width=100 height=100>" : "<img src='imagens/foto_p.jpg' width=100 height=100>");

    if ($card == 0) {
        echo "<tr>";
    }

    $htmlCarrinho = ($qtde_estoque > 0) ? "<a href='carrinho.php?id_produto=$id_produto&operacao=INCLUIR'><button>Comprar</button></a>" : "<button disabled>Indisponível</button>";

    echo "<head>
    <style>
    .card{
        display: flex;
        align-content: center;
        justify-content: center;
    </style> 
    </head>
    
    <body>
        <td>
            <center>
            $htmlFoto
            <br><strong>$nome</strong><br>
            <i>R$ $valor_unitario</i><br>
            $htmlCarrinho
            </center>
        </td>
    </body>";

    $card++;
    if ($card == 4) {
        echo "</tr>";
        $card = 0;
    }
}

if ($card > 0) {
    echo "</tr>";
}

echo "</table>";
