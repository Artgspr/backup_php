<?php 

   // mostra TODOS os erros do php
   ini_set ( 'display_errors' , 1); 
   error_reporting (E_ALL);

   //// PARA ENVIO DE EMAILS PHPMAILER //////
   //require 'PHPMailer\PHPMailer\src\Exception.php';
   require 'PHPMailer\PHPMailer\src\PHPMailer.php';
   require 'PHPMailer\PHPMailer\src\SMTP.php';
   //////////////////////////////////////////

   // inicia a sessao
   session_start();
   
  //////////////////////////////////////////////////////////////// 
  // Envio de emails
  // Marcelo C Peres 2023
  /* Exemplo: 
     if ( EnviaEmail ('fulano@fulano','Feliz Aniversario',
                      '<html><body>Feliz niver</body></html>') 
     {
      echo 'enviado com sucesso';
     }
  */   
     
  ////////////////////////////////////////////////////////////////
  function EnviaEmail ( $pEmailDestino, $pAssunto, $pHtml, 
                        $pUsuario = "marcelocabello@projetoscti.com.br", 
                        $pSenha = "MarceloC@belo", 
                        $pSMTP = "smtp.projetoscti.com.br" )   
  {
  
    
      
   try {
 
     //cria instancia de phpmailer
     echo "<br>Tentando enviar para $pEmailDestino...";
     $mail = new PHPMailer(); 
     $mail->IsSMTP();  
  
     // servidor smtp
     $mail->Host = $pSMTP;
     $mail->SMTPAuth = true;      // requer autenticacao com o servidor                         
     $mail->SMTPSecure = 'tls';                            
      
     $mail-> SMTPOptions = array (
       'ssl' => array (
       'verificar_peer' => false,
       'verify_peer_name' => false,
       'allow_self_signed' => true ) );
      
     $mail->Port = 587;      
      
     $mail->Username = $pUsuario; 
     $mail->Password = $pSenha; 
     $mail->From = $pUsuario; 
     $mail->FromName = "Suporte de senhas"; 
  
     $mail->AddAddress($pEmailDestino, "Usuario"); 
     $mail->IsHTML(true); 
     $mail->Subject = $pAssunto; 
     $mail->Body = $pHtml;
     $enviado = $mail->Send(); 
       
     if (!$enviado) {
        echo "<br>Erro: " . $mail->ErrorInfo;
     } else {
        echo "<br><b>Enviado!</b>";
     }
     return $enviado;         
      
   } catch (Exception $e) {
     echo $e->errorMessage(); // erros do phpmailer
   } catch (Exception $e) {
     echo $e->getMessage(); // erros da aplica��o - gerais
   }      
  }


  ///////////////////////////////////////////////////////////
  /*
  * ExecutaSQL frases sql
  * marcelo c peres - 2023
  */

  function ExecutaSQL( $paramConn, $paramSQL ) 
  {
    // exec eh usado para update, delete, insert
    // eh um metodo da conexao
    // retorna TRUE se houve linhas afetadas
    $linhas = $paramConn->exec($paramSQL);
    return ($linhas > 0);
  }


  ///////////////////////////////////////////////////////////
  // ValorSQL 
  // retorna o valor de um campo de um select
  // Set 2023 - Marcelo C Peres 
  function ValorSQL( $pConn, $pSQL ) 
  {
   $linha = $pConn->query($pSQL)->fetch();
   if ( $linha ) { 
       return $linha[0]; // equivale a retornar o valor do campo
   } else { 
       return "0"; 
   }
  }

  ///////////////////////////////////////////////////////////
  /**
  * Funcao para gerar senhas aleatorias
  *
  * @author    Thiago Belem <contato@thiagobelem.net>
  *
  * @param integer $tamanho Tamanho da senha a ser gerada
  * @param boolean $maiusculas Se ter� letras mai�sculas
  * @param boolean $numeros Se ter� n�meros
  * @param boolean $simbolos Se ter� s�mbolos
  *
  * @return string A senha gerada
  */
  
  function GeraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
  {
    //$lmin = 'abcdefghijklmnopqrstuvwxyz';
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '1234567890';
    $simb = '!@#$%*-';
    $retorno = '';
    $caracteres = '';

    //$caracteres .= $lmin;
    if ($maiusculas) $caracteres .= $lmai;
    if ($numeros)    $caracteres .= $num;
    if ($simbolos)   $caracteres .= $simb;

    $len = strlen($caracteres);
    
    for ($n = 1; $n <= $tamanho; $n++) {
        $rand = mt_rand(1, $len);
        $retorno .= $caracteres[$rand-1];
    }
    
    return $retorno;
  }

  ///////////////////////////////////////////////////////////
  // util.php //// funcao de conexao 14-8-2023
  function conecta ($params = "")
  // declare dessa forma pra poder omitir o parametro
  {
    if ($params == "") {
       $params="pgsql:host=pgsql.projetoscti.com.br; dbname=projetoscti13; 
       user=projetoscti13; password=eq12951";
    }
    
    $varConn = new PDO($params);
    if (!$varConn) {
      echo "Nao foi possivel conectar";
    } else { return $varConn; }

  }

  ///////////////////////////////////////////////////////////
  //////  funcao de login
  //////  11-9-2023
  function ValidaLogin ($paramLogin, $paramSenha, &$paramAdmin)  
  {
   $conn = conecta();  
   $varSQL = " select senha,admin from usuario 
               where email = :paramLogin "; 
   $select = $conn->prepare($varSQL);
   $select->bindParam(':paramLogin',$paramLogin);
   $select->execute();
   $linha = $select->fetch();

   if ( $linha ) {
        $paramAdmin = $linha['admin'] ;
        return $linha['senha'] == $paramSenha;  
   } else {
        $paramAdmin = false;
        return false;  
   } 
  }

  ///////////////////////////////////////////////////////////
  //////  funcao de definir cookie
  //////  11-9-2023
  function DefineCookie($paramNome, $paramValor, $paramMinutos) 
  {
   echo "Cookie: $paramNome Valor: $paramValor";  
   setcookie($paramNome, $paramValor, time() + $paramMinutos * 60); 
  }
?>
