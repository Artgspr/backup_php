<?php

include("cabecalho.php");
$conn = conecta();

if ($_GET) {
    $id = $_GET['id_produto'];

    $varSQL = "UPDATE produto SET excluido = TRUE WHERE id_produto = :id";
    $update = $conn->prepare($varSQL);
    $update->bindParam(':id', $id);
    $update->execute();

    echo "<br> O produto foi marcado como excluído.<br>";
    echo "<br><a href='produto.php'><button>Voltar</button></a>";
}


