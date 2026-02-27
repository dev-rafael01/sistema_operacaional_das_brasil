<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Usuario.php';


class AuthController
{
   public function login()
    {

        if (session_status() === PHP_SESSION_NONE) 
            {
                session_start();
             }
        $erros = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {

            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // =========================
            // VALIDAÇÃO
            // =========================
            if (trim($email) === '') {
                $erros[] = 'E-mail é obrigatório';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erros[] = 'E-mail inválido';
            }

            if (trim($password) === '') {
                $erros[] = 'Senha é obrigatória';
            }

            // =========================
            // SE NÃO HOUVER ERROS → BANCO
            // =========================
            if (empty($erros))
            {

                $db = new Database();
                $pdo = $db->getConnection();

                $userModel = new Usuarios($pdo);
                $usuario = $userModel->findByEmail($email);

                if (!$usuario) {
                    $erros[] = 'Usuário não encontrado';
                } elseif (!password_verify($password, $usuario['password'])) {
                    $erros[] = 'Senha incorreta';
                } else {
                    // LOGIN OK                

                    $_SESSION['usuario'] = [
                        'id'  => (int)$usuario['id'],
                        'name' => $usuario['name'],
                        'sobrenome' => $usuario['sobrenome'],
                        'nome_completo' => ucwords(strtolower($usuario['name'] . ' ' . $usuario['sobrenome'])),
                        'email'  => $usuario['email'],
                        'cargo'  => trim((string)$usuario['cargo']),
                        'is_admin' => ((int)$usuario['is_admin'] === 1),
                    ];

                    //redirecione para Painel
                    header('location: /sistemaoperacionaldasbrasil/home/logado');
                    exit;
                }
            }
        }

        // =========================
        // CARREGAR VIEW
        // =========================
        $bodyClass = 'page-login';
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/home/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function registro()
{
    $erros = [];

    $name = '';
    $sobrenome = '';
    $email = '';
    $telefone = '';
    $cargo = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = trim($_POST['name'] ?? '');
        $sobrenome = trim($_POST['sobrenome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '') $erros[] = 'Nome obrigatório';
        if ($sobrenome === '') $erros[] = 'Sobrenome obrigatório';

        if ($email === '') {
            $erros[] = 'Email obrigatório';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'Email inválido';
        }

        if (trim($password) === '') $erros[] = 'Senha obrigatória';
        if ($cargo === '') $erros[] = 'Cargo obrigatório';
        if ($telefone === '') $erros[] = 'Telefone obrigatório';


        if (empty($erros)) {
            $db = new Database();
            $pdo = $db->getConnection();

            $userModel = new Usuarios($pdo);

            if ($userModel->emailExists($email)) {
                $erros[] = 'Esse email já está cadastrado';
            } else {
                try {
                    $id = $userModel->create($name, $sobrenome, $email, $telefone, $cargo, $password);
                    $_SESSION['success'] = 'Cadastro realizado com sucesso! Faça login.' . $id;
                    header('Location: /sistemaoperacionaldasbrasil/auth/login');
                    exit;
                } catch (\PDOException $e) {
                    die($e->getMessage());
                    // $erros[] = 'Erro ao cadastrar. Tente novamente.';
                }
            }
        }
    }

    require __DIR__ . '/../views/layouts/header.php';
    require __DIR__ . '/../views/auth/registro.php';
    require __DIR__ . '/../views/layouts/footer.php';
}

     public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        header('Location: /sistemaoperacionaldasbrasil/');
         exit;

    }

    public function forgot()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    require __DIR__ . '/../views/layouts/header.php';
    require __DIR__ . '/../views/auth/forgot.php';
    require __DIR__ . '/../views/layouts/footer.php';
}

public function sendReset()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    $email = trim($_POST['email'] ?? '');

    // mensagem padrão (não revela se existe ou não)
    $_SESSION['msg'] = 'Se esse e-mail estiver cadastrado, você receberá um link para redefinir sua senha.';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /sistemaoperacionaldasbrasil/auth/forgot');
        exit;
    }

    $db = new Database();
    $pdo = $db->getConnection();

    $userModel = new Usuarios($pdo);
    $usuario = $userModel->findByEmail($email);

    if (!$usuario) {
        header('Location: /sistemaoperacionaldasbrasil/auth/forgot');
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiresAt = (new DateTime('+30 minutes'))->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at)
                           VALUES (:uid, :th, :exp)");
    $stmt->execute([
        'uid' => (int)$usuario['id'],
        'th'  => $tokenHash,
        'exp' => $expiresAt
    ]);

    // ✅ por enquanto (DEV): mostra o link na tela (sem email real)
    $_SESSION['debug_reset_link'] = "/sistemaoperacionaldasbrasil/auth/reset?token=" . urlencode($token);

    header('Location: /sistemaoperacionaldasbrasil/auth/forgot');
    exit;
}

public function reset()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    $token = $_GET['token'] ?? '';
    if ($token === '') {
        http_response_code(400);
        echo "Token inválido.";
        exit;
    }

    $tokenHash = hash('sha256', $token);

    $db = new Database();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("SELECT * FROM password_resets
                           WHERE token_hash = :th
                             AND used_at IS NULL
                             AND expires_at > NOW()
                           ORDER BY id DESC
                           LIMIT 1");
    $stmt->execute(['th' => $tokenHash]);
    $reset = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$reset) {
        http_response_code(400);
        echo "Link expirado ou inválido.";
        exit;
    }

    require __DIR__ . '/../views/layouts/header.php';
    require __DIR__ . '/../views/auth/reset.php';
    require __DIR__ . '/../views/layouts/footer.php';
}

public function updatePassword()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    $token = $_POST['token'] ?? '';
    $p1 = $_POST['password'] ?? '';
    $p2 = $_POST['password_confirm'] ?? '';

    if ($token === '' || $p1 === '' || $p2 === '' || $p1 !== $p2) {
        $_SESSION['msg'] = 'Verifique as senhas.';
        header('Location: /sistemaoperacionaldasbrasil/auth/reset?token=' . urlencode($token));
        exit;
    }

    $tokenHash = hash('sha256', $token);

    $db = new Database();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("SELECT * FROM password_resets
                           WHERE token_hash = :th
                             AND used_at IS NULL
                             AND expires_at > NOW()
                           ORDER BY id DESC
                           LIMIT 1");
    $stmt->execute(['th' => $tokenHash]);
    $reset = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$reset) {
        $_SESSION['msg'] = 'Link expirado ou inválido.';
        header('Location: /sistemaoperacionaldasbrasil/auth/forgot');
        exit;
    }

    $userModel = new Usuarios($pdo);
    $userModel->updatePasswordById((int)$reset['user_id'], $p1);

    $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id")
        ->execute(['id' => (int)$reset['id']]);

    $_SESSION['success'] = 'Senha atualizada com sucesso. Faça login.';
    header('Location: /sistemaoperacionaldasbrasil/auth/login');
    exit;
}

}

?>