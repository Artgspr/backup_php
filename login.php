<?php

include("cabecalho.php");  

$formularioVisivel = true; 

if ($_POST) {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $_SESSION['sessaoConectado'] = ValidaLogin($login, $senha, $eh_admin);
    $_SESSION['sessaoAdmin'] = $eh_admin;

    if (ValidaLogin($login, $senha, $eh_admin)) {
        $_SESSION['sessaoConectado'] = true;
        $_SESSION['sessaoLogin'] = $login;
        $_SESSION['sessaoAdmin'] = $eh_admin;
        setcookie('loginCookie', $login, time() + 86400);
        header('Location: index.php');
        exit();  
    } else {
        $formularioVisivel = false; 
        echo "<b>Usuario ou senha nao encontrado</b>
            <br><br><a href='index.php'><button>Voltar</button> </a>";
    }
}
if (isset($_COOKIE['loginCookie'])) {
    $loginCookie = $_COOKIE['loginCookie'];
} else {
    $loginCookie = '';
}

if ($formularioVisivel) {
    echo "
    <form name='formlogin' method='post' action=''>
    <br>Login<input type='text' name='login' value='$loginCookie'>
    <br>Senha<input type='password' name='senha'>  <!-- Corrige o tipo de input -->
    <br><input type='submit' value='Enviar'>  <!-- Corrige o texto do botão -->
    </form>";
}

?>
