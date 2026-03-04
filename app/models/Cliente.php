<?php

class Cliente
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT id, nome FROM clientes ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nome FROM clientes WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $nome): int
    {
        $sql = "INSERT INTO clientes (nome) VALUES (:nome)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nome' => $nome]);
        
        return (int)$this->pdo->lastInsertId();
    }
}
?>