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

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['usuario']) || empty($_SESSION['usuario']['is_admin'])) {
            http_response_code(403);
            echo "Acesso negado.";
            exit;
        }

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/cliente/create.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['usuario']) || empty($_SESSION['usuario']['is_admin'])) {
            http_response_code(403);
            echo "Acesso negado.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            echo "Nome do local é obrigatório.";
            exit;
        }

        $db = new Database();
        $pdo = $db->getConnection();
        $model = new Cliente($pdo);

        $model->create($nome);

        header("Location: /sistemaoperacionaldasbrasil/cliente/index");
        exit;
    }

    public function delete()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['usuario']) || empty($_SESSION['usuario']['is_admin'])) {
            http_response_code(403);
            echo "Acesso negado.";
            exit;
        }

        $clienteId = (int)($_GET['id'] ?? 0);

        if ($clienteId <= 0) {
            http_response_code(400);
            echo "Cliente inválido.";
            exit;
        }

        $db = new Database();
        $pdo = $db->getConnection();

        // 🔥 1) Buscar checklist para apagar vídeos
        $stmt = $pdo->prepare("SELECT * FROM checklist WHERE cliente_id = :cid");
        $stmt->execute(['cid' => $clienteId]);
        $checklist = $stmt->fetch();

        if ($checklist) {

            $videos = [
                'video_painel_eletrico',
                'video_sistema_primario',
                'video_sistema_secundario',
                'video_sistema_terciario'
            ];

            foreach ($videos as $campo) {
                if (!empty($checklist[$campo])) {

                    $arquivo = __DIR__ . '/../../public' .
                        str_replace('/sistemaoperacionaldasbrasil/public', '', $checklist[$campo]);

                    if (file_exists($arquivo)) {
                        unlink($arquivo);
                    }
                }
            }

            // 🔥 2) Apagar checklist
            $pdo->prepare("DELETE FROM checklist WHERE cliente_id = :cid")
                ->execute(['cid' => $clienteId]);
        }

        // 🔥 3) Apagar cliente
        $pdo->prepare("DELETE FROM clientes WHERE id = :cid")
            ->execute(['cid' => $clienteId]);

        header("Location: /sistemaoperacionaldasbrasil/cliente/index");
        exit;
    }
}