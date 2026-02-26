<?php

class Usuarios
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
                    //encontrarPorEmail
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, name, sobrenome, email, cargo, is_admin, password
         FROM usuarios
          WHERE email =:email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);


        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $usuario ?: null;
    }

    public function create($name, $sobrenome, $email, $telefone, $cargo, $passwordPlain) : int
    {
        $hast = password_hash($passwordPlain, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (name, sobrenome, email, telefone, cargo, password) VALUES (:name, :sobrenome, :email, :telefone, :cargo, :password)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'sobrenome' => $sobrenome,
            'email' => $email,
            'telefone' => $telefone,
            'cargo' => $cargo,
            'password' => $hast,
        ]);

        return (int)$this->pdo->lastInsertId();      
    }

    public function emailExists(string $email): bool
    {
        $sql = "SELECT 1 FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    public function updatePasswordById(int $id, string $passwordPlain): void
{
    $hash = password_hash($passwordPlain, PASSWORD_DEFAULT);
    $stmt = $this->pdo->prepare("UPDATE usuarios SET password = :p WHERE id = :id LIMIT 1");
    $stmt->execute(['p' => $hash, 'id' => $id]);
}


        
               
}

?>