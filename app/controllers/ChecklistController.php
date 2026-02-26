<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Checklist.php';

class ChecklistController
{
    public function save()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (empty($_SESSION['usuario'])) {
            header('Location: /sistemaoperacionaldasbrasil/');
            exit;
        }

        // ✅ PERMISSÃO: só quem pode editar
        if (empty($_SESSION['usuario']['cant_edit'])) {
            http_response_code(403);
            echo "Acesso negado: somente visualização.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Método inválido.";
            exit;
        }

        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        if ($clienteId <= 0) {
            http_response_code(400);
            echo "Cliente inválido.";
            exit;
        }

        $data = [
            'cliente_id'    => $clienteId,
            'limpeza'       => (int)($_POST['limpeza'] ?? 0),
            'data_limpeza'  => trim($_POST['data_limpeza'] ?? ''),
            'cartucho'      => (int)($_POST['cartucho'] ?? 0),
            'data_cartucho' => trim($_POST['data_cartucho'] ?? ''),
            'observacoes'   => trim($_POST['observacoes'] ?? ''),
            'updated_by'    => (int)($_SESSION['usuario']['id'] ?? 0),
        ];

        $db = new Database();
        $pdo = $db->getConnection();

        $model = new Checklist($pdo);
        $model->upsert($data);

        header("Location: /sistemaoperacionaldasbrasil/cliente/show?id={$clienteId}");
        exit;
    }
}