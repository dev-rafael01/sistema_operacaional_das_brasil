<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Checklist.php';

class ClienteController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['usuario'])) {
            header('Location: /sistemaoperacionaldasbrasil/');
            exit;
        }

        $db = new Database();
        $pdo = $db->getConnection();
        // var_dump(class_exists('Cliente'));
        // exit;

        $clienteModel = new Cliente($pdo);
        $clientes = $clienteModel->all();

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/cliente/clientes.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function show()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['usuario'])) {
            header('Location: /sistemaoperacionaldasbrasil/');
            exit;
        }

        $clienteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($clienteId <= 0) {
            http_response_code(400);
            echo "Cliente inválido.";
            exit;
        }

        $db = new Database();
        $pdo = $db->getConnection();

        $clienteModel = new Cliente($pdo);
        $checkModel = new Checklist($pdo);

        $cliente = $clienteModel->find($clienteId);
        if (!$cliente) {
            http_response_code(404);
            echo "Cliente não encontrado.";
            exit;
        }

        $checklist = $checkModel->findByClienteId($clienteId);

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/cliente/show.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }
}