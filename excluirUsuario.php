<?php

include("cabecalho.php");
$conn = conecta();

if ($_GET) {
    $id = $_GET['id_usuario'];

    $varSQL = "UPDATE usuario SET excluido = TRUE WHERE id_usuario = :id";
    $update = $conn->prepare($varSQL);
    $update->bindParam(':id', $id);
    $update->execute();

    echo "<br> O usuario foi marcado como excluído.<br>";
    echo "<br><a href='usuario.php'><button>Voltar</button></a>";
}


