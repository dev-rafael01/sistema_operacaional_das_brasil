<?php 
  if(session_status() === PHP_SESSION_NONE){
    session_start();
  }
 
 ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- <link rel="stylesheet" href="/sistemaoperacionaldasbrasil/public/assets/style.css"> -->
      <link rel="stylesheet" href="/sistemaoperacionaldasbrasil/public/assets/style.css?v=<?= time() ?>">
      <link rel="stylesheet" href="/sistemaoperacionaldasbrasil/public/assets/responsive.css<?time() ?>">
     
    <title>Sistem Operacional Das Brasil</title>
 

</head>
<?php
  $isLogged = !empty($_SESSION['usuario']);
  $pageClass = $isLogged ? 'page-app' : 'page-login';
?>
<body class="<?= $pageClass ?>">

    
