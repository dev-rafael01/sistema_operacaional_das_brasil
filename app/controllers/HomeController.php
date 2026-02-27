<?php

class HomeController
{
    public function index()
    {
       if(session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }
        if (empty($_SESSION['usuario'])) 
        {
            header('Location: /sistemaoperacionaldasbrasil/');
            exit;
        }
           require __DIR__ . '/../views/layouts/header.php';
           require __DIR__ . '/../views/home/index.php';
           require __DIR__ . '/../views/layouts/footer.php';
    }


    public function logado()
    {
            if(session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (empty($_SESSION['usuario'])) {
                header('Location: /sistemaoperacionaldasbrasil/');
                exit;
            }
            $bodyClass = 'page-app';
            require __DIR__ . '/../views/layouts/header.php';
            require __DIR__ . '/../views/home/logado.php';
            require __DIR__ . '/../views/layouts/footer.php';
    }       

}
?>