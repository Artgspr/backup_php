<?php
include ("util.php");

if (isset($_SESSION['sessaoConectado']) && $_SESSION['sessaoConectado']) {
    $sessaoConectado = $_SESSION['sessaoConectado'];
    $login = $_SESSION['sessaoLogin'];
    $idSessao = session_id();

    echo "
          <a href='index.php'><img src='imagens/foto_u.jpg' width=60 height=50></img></a>
          <br> 
          Olá, $login (id sessão: <b>$idSessao</b>)
          <br><a href='logout.php'><button type='button'>Sair</button></a><br>";

    if ($_SESSION['sessaoAdmin']) {
        echo "<br><table align='left'>
                <tr>
                    <td><a href='usuario.php'><button type='button'>Usuarios</button></a></td>
                    <td><a href='produto.php'><button type='button'>Produtos</button></a></td>
                    <td><a href='relatorio.php'><button type='button'>Relatorios</button></a></td>
                </tr>
              </table>";
    }
} else {
    echo "<a href='login.php'><button type='button'>Login</button></a>";
}

echo "<br><br><hr>";
?>