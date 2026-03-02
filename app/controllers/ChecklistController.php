<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Checklist.php';

class ChecklistController
{
    private function canEdit(): bool
    {
        return !empty($_SESSION['usuario']['is_admin']);
    }

    private function uploadVideo(string $inputName, int $clienteId): ?string
    {
    if (empty($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$inputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Limite (50MB)
    $maxSize = 50 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return null;
    }

    // Validar MIME
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);

    if (strpos($mime, 'video/') !== 0) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['mp4', 'mov', 'm4v', 'webm'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    // Conexão para buscar vídeo antigo
    $db = new Database();
    $pdo = $db->getConnection();
    $model = new Checklist($pdo);
    $existente = $model->findByClienteId($clienteId);

    // 🔥 APAGA VÍDEO ANTIGO (se existir)
    if (!empty($existente[$inputName])) {

        $arquivoAntigo = __DIR__ . '/../../public' . 
            str_replace('/sistemaoperacionaldasbrasil/public', '', $existente[$inputName]);

        if (file_exists($arquivoAntigo)) {
            unlink($arquivoAntigo);
        }
    }

    // Pasta destino
    $dir = __DIR__ . '/../../public/uploads/videos/cliente_' . $clienteId;
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $safeName = $inputName . '_' . date('Ymd_His') . '.' . $ext;
    $dest = $dir . '/' . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }

    return '/sistemaoperacionaldasbrasil/public/uploads/videos/cliente_' . $clienteId . '/' . $safeName;
    }

    public function save()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (empty($_SESSION['usuario'])) {
        header('Location: /sistemaoperacionaldasbrasil/');
        exit;
    }

    // ✅ Somente admin edita
    if (empty($_SESSION['usuario']['is_admin'])) {
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

    $db = new Database();
    $pdo = $db->getConnection();
    $model = new Checklist($pdo);

    $existente = $model->findByClienteId($clienteId);

    // ==============================
    // 🔥 EXCLUSÃO MANUAL DE VÍDEO
    // ==============================
    if (!empty($_POST['delete_video'])) {

        $campoVideo = $_POST['delete_video'];

        if (!empty($existente[$campoVideo])) {

            $arquivo = __DIR__ . '/../../public' .
                str_replace('/sistemaoperacionaldasbrasil/public', '', $existente[$campoVideo]);

            if (file_exists($arquivo)) {
                unlink($arquivo);
            }

            $stmt = $pdo->prepare("UPDATE checklist SET {$campoVideo} = NULL WHERE cliente_id = :cid");
            $stmt->execute(['cid' => $clienteId]);
        }

        header("Location: /sistemaoperacionaldasbrasil/cliente/show?id={$clienteId}");
        exit;
    }

    // ==============================
    // UPLOAD DE VÍDEOS
    // ==============================

    $videoPainel = $this->uploadVideo('video_painel_eletrico', $clienteId)
        ?? ($existente['video_painel_eletrico'] ?? null);

    $videoPrimario = $this->uploadVideo('video_sistema_primario', $clienteId)
        ?? ($existente['video_sistema_primario'] ?? null);

    $videoSecundario = $this->uploadVideo('video_sistema_secundario', $clienteId)
        ?? ($existente['video_sistema_secundario'] ?? null);

    $videoTerciario = $this->uploadVideo('video_sistema_terciario', $clienteId)
        ?? ($existente['video_sistema_terciario'] ?? null);

    // ==============================
    // DADOS DO CHECKLIST
    // ==============================

    $data = [
        'cliente_id' => $clienteId,

        'limpeza_tanque_intermediario' => (int)($_POST['limpeza_tanque_intermediario'] ?? 0),
        'data_limpeza_tanque_intermediario' => trim($_POST['data_limpeza_tanque_intermediario'] ?? ''),

        'limpeza_sedimentador' => (int)($_POST['limpeza_sedimentador'] ?? 0),
        'data_limpeza_sedimentador' => trim($_POST['data_limpeza_sedimentador'] ?? ''),

        'troca_cartucho_terciario' => (int)($_POST['troca_cartucho_terciario'] ?? 0),
        'data_troca_cartucho_terciario' => trim($_POST['data_troca_cartucho_terciario'] ?? ''),

        'cloro_ok' => (int)($_POST['cloro_ok'] ?? 0),
        'quantidade_cloro' => trim($_POST['quantidade_cloro'] ?? ''),

        'tem_begues_cartucho' => (int)($_POST['tem_begues_cartucho'] ?? 0),
        'quantidade_begues_cartucho' => trim($_POST['quantidade_begues_cartucho'] ?? ''),

        'observacoes' => trim($_POST['observacoes'] ?? ''),

        'video_painel_eletrico' => $videoPainel,
        'video_sistema_primario' => $videoPrimario,
        'video_sistema_secundario' => $videoSecundario,
        'video_sistema_terciario' => $videoTerciario,

        'updated_by' => (int)$_SESSION['usuario']['id'],
    ];

    $model->upsert($data);

    header("Location: /sistemaoperacionaldasbrasil/cliente/show?id={$clienteId}");
    exit;
}
    public function deleteVideo()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (empty($_SESSION['usuario']) || empty($_SESSION['usuario']['is_admin'])) {
        http_response_code(403);
        exit('Acesso negado.');
    }

    $clienteId = (int)($_POST['cliente_id'] ?? 0);
    $campo = $_POST['campo'] ?? '';

    $permitidos = [
        'video_painel_eletrico',
        'video_sistema_primario',
        'video_sistema_secundario',
        'video_sistema_terciario'
    ];

    if (!$clienteId || !in_array($campo, $permitidos, true)) {
        exit('Dados inválidos.');
    }

    $db = new Database();
    $pdo = $db->getConnection();

    $model = new Checklist($pdo);
    $checklist = $model->findByClienteId($clienteId);

    if (!empty($checklist[$campo])) {

        $arquivoFisico = __DIR__ . '/../../public' . str_replace('/sistemaoperacionaldasbrasil/public', '', $checklist[$campo]);

        if (file_exists($arquivoFisico)) {
            unlink($arquivoFisico);
        }

        $stmt = $pdo->prepare("UPDATE checklist SET $campo = NULL WHERE cliente_id = :cid");
        $stmt->execute(['cid' => $clienteId]);
    }

    header("Location: /sistemaoperacionaldasbrasil/cliente/show?id={$clienteId}");
    exit;
}
}