<?php

require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ClienteController.php';
require_once __DIR__ . '/../controllers/ChecklistController.php';

class Router
{
    public function dispatch($url)
    {      
        $url = trim($url, '/');

         $partes = $url ? explode('/', $url) : [];

         $controllerBase = $partes[0] ?? 'Auth';

         $metodo = $partes[1] ??  'login';

         $controllerBase = ucfirst($controllerBase) . "Controller";

         if (!class_exists($controllerBase))
            {
                http_response_code(404);
                echo "Controller não encontrado: " . htmlspecialchars($controllerBase);
                 exit;
            }

         $controller = new $controllerBase();

         if(!method_exists($controller, $metodo))
            {
                http_response_code(404);
                echo "Metodo não encontrado: " . htmlspecialchars($metodo);
                exit;
            }
         $controller->$metodo();



    }
    
}


?>