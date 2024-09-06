<?php
include('cabecalho.php');
$conn = conecta();
$session_id = session_id();
$status = 'PENDENTE';
$id_compra = null;

// Criando ou obtendo a compra associada à sessão atual
$varSQL = "SELECT id_compra, status FROM compra WHERE sessao = :sessao";
$select = $conn->prepare($varSQL);
$select->execute([':sessao' => $session_id]);
$compra = $select->fetch();

if (!$compra) {
    // Inserir nova compra se não existir
    $varSQL = "INSERT INTO compra (status, data, sessao) VALUES (:status, CURRENT_DATE, :sessao)";
    $select = $conn->prepare($varSQL);
    $select->execute([':status' => $status, ':sessao' => $session_id]);
    $id_compra = $conn->lastInsertId();
} else {
    // Atribuir valores da compra existente
    $id_compra = $compra['id_compra'];
    $status = $compra['status'];
}

if (isset($_SESSION['sessaoConectado'])) {
    // Obter o id_usuario a partir do e-mail armazenado na sessão
    $email = $_SESSION['sessaoLogin'];
    $varSQL = "SELECT id_usuario FROM usuario WHERE email = :email";
    $select = $conn->prepare($varSQL);
    $select->execute([':email' => $email]);
    $usuario = $select->fetch();

    if ($usuario) {
        $id_usuario = $usuario['id_usuario'];
        
        // Atualizar a tabela compra com o id_usuario
        $varSQL = "UPDATE compra SET fk_id_usuario = :id_usuario WHERE id_compra = :id_compra";
        $select = $conn->prepare($varSQL);
        $select->execute([':id_usuario' => $id_usuario, ':id_compra' => $id_compra]);
    }
}  

// Função para atualizar o grid do carrinho
function AtualizaGride($conn, $id_compra, $status) {
    echo "<h3>Compra ID: $id_compra</h3>";

    $varSQL = "
        SELECT p.id_produto, p.nome, p.descricao, cp.valor_unitario, cp.quantidade, (cp.valor_unitario * cp.quantidade) AS subtotal
        FROM compra_produto cp
        JOIN produto p ON cp.fk_id_produto = p.id_produto
        WHERE cp.fk_id_compra = :fk_id_compra
    ";
    $select = $conn->prepare($varSQL);
    $select->execute([':fk_id_compra' => $id_compra]);

    echo "<table border='1' width='100%'>";
    $card = 0;
    $total = 0;

    while ($linha = $select->fetch()) {
        $id_produto = $linha['id_produto'];
        $nome = $linha['nome'];
        $descricao = $linha['descricao'];
        $valor_unitario = number_format($linha['valor_unitario'], 2, ',', '.');
        $quantidade = $linha['quantidade'];
        $subtotal = number_format($linha['subtotal'], 2, ',', '.');
        $total += $linha['subtotal'];

        $foto = "imagens/p" . $id_produto . ".jpg";
        $htmlFoto = (file_exists($foto) ? "<img src='$foto' width=100 height=100>" : "<img src='imagens/foto_p.jpg' width=100 height=100>");

        if ($card == 0) {
            echo "<tr>";
        }

        $htmlAcoes = "
            <a href='carrinho.php?id_produto=$id_produto&operacao=INCLUIR'><button>Adicionar</button></a>
            <a href='carrinho.php?id_produto=$id_produto&operacao=EXCLUIR'><button>Remover</button></a>
        ";

        echo "<td>
                <center>
                $htmlFoto
                <br><strong>$nome</strong><br>
                <i>R$ $valor_unitario</i><br>
                Quantidade: $quantidade<br>
                Subtotal: R$ $subtotal<br>
                $htmlAcoes
                </center>
              </td>";

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

    echo "<h4>Total: R$ " . number_format($total, 2, ',', '.') . "</h4>";
    echo "<h4>Status: $status</h4>";

    if (isset($_SESSION['sessaoConectado']) && $total > 0 && $status === 'PENDENTE') {
        echo "<a href='carrinho.php?operacao=FECHAR'><button>Fechar compra</button></a>";
    }
}

// Tratamento das operações
if (isset($_GET['operacao']) && isset($_GET['id_produto'])) {
    $operacao = $_GET['operacao'];
    $id_produto = $_GET['id_produto'];

    $varSQL = "SELECT qtde_estoque, valor_unitario FROM produto WHERE id_produto = :id_produto";
    $select = $conn->prepare($varSQL);
    $select->execute([':id_produto' => $id_produto]);
    $produto = $select->fetch();

    if ($produto) {
        $qtde_estoque = $produto['qtde_estoque'];
        $valor_unitario = $produto['valor_unitario'];

        if ($operacao === 'INCLUIR') {
            $varSQL = "SELECT quantidade FROM compra_produto WHERE fk_id_compra = :fk_id_compra AND fk_id_produto = :fk_id_produto";
            $select = $conn->prepare($varSQL);
            $select->execute([':fk_id_compra' => $id_compra, ':fk_id_produto' => $id_produto]);
            $compraProduto = $select->fetch();

            if (!$compraProduto) {
                // Adiciona o produto ao carrinho
                $varSQL = "INSERT INTO compra_produto (fk_id_compra, fk_id_produto, quantidade, valor_unitario) VALUES (:fk_id_compra, :fk_id_produto, 1, :valor_unitario)";
                $insert = $conn->prepare($varSQL);
                $insert->execute([':fk_id_compra' => $id_compra, ':fk_id_produto' => $id_produto, ':valor_unitario' => $valor_unitario]);
            } elseif ($qtde_estoque > $compraProduto['quantidade']) {
                // Incrementa a quantidade
                $varSQL = "UPDATE compra_produto SET quantidade = quantidade + 1 WHERE fk_id_compra = :fk_id_compra AND fk_id_produto = :fk_id_produto";
                $update = $conn->prepare($varSQL);
                $update->execute([':fk_id_compra' => $id_compra, ':fk_id_produto' => $id_produto]);
            }
        } elseif ($operacao === 'EXCLUIR') {
            $varSQL = "SELECT quantidade FROM compra_produto WHERE fk_id_compra = :fk_id_compra AND fk_id_produto = :fk_id_produto";
            $select = $conn->prepare($varSQL);
            $select->execute([':fk_id_compra' => $id_compra, ':fk_id_produto' => $id_produto]);
            $compraProduto = $select->fetch();

            if ($compraProduto && $compraProduto['quantidade'] > 1) {
                // Decrementa a quantidade
                $varSQL = "UPDATE compra_produto SET quantidade = quantidade - 1 WHERE fk_id_compra = :fk_id_compra AND fk_id_produto = :fk_id_produto";
                $update = $conn->prepare($varSQL);
                $update->execute([':fk_id_compra' => $id_compra, ':fk_id_produto' => $id_produto]);
            } else {
                // Remove o produto do carrinho
                $varSQL = "DELETE FROM compra_produto WHERE fk_id_compra = :fk_id_compra AND fk_id_produto = :fk_id_produto";
                $delete = $conn->prepare($varSQL);
                $delete->execute([':fk_id_compra' => $id_compra, ':fk_id_produto' => $id_produto]);
            }
        } elseif ($operacao === 'FECHAR') {
            echo "<form action='pagar.php' method='POST'>";
            echo "<input type='hidden' name='id_compra' value='$id_compra'>";
            echo "<input type='submit' value='Confirmar pagamento'>";
            echo "</form>";
        }
    }

    AtualizaGride($conn, $id_compra, $status);
} else {
    AtualizaGride($conn, $id_compra, $status);
}
?>
