<?php

class Checklist
{
    public function __construct(private \PDO $pdo) {}

    public function findByClienteId(int $clienteId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM checklist WHERE cliente_id = :cid LIMIT 1");
        $stmt->execute(['cid' => $clienteId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsert(array $data): void
    {
        // Se limpeza/cartucho = 0, datas viram NULL
        if ((int)$data['limpeza'] !== 1) $data['data_limpeza'] = null;
        if ((int)$data['cartucho'] !== 1) $data['data_cartucho'] = null;

        $sql = "
            INSERT INTO checklist
              (cliente_id, limpeza, data_limpeza, cartucho, data_cartucho, observacoes, updated_by)
            VALUES
              (:cliente_id, :limpeza, :data_limpeza, :cartucho, :data_cartucho, :observacoes, :updated_by)
            ON DUPLICATE KEY UPDATE
              limpeza       = VALUES(limpeza),
              data_limpeza  = VALUES(data_limpeza),
              cartucho      = VALUES(cartucho),
              data_cartucho = VALUES(data_cartucho),
              observacoes   = VALUES(observacoes),
              updated_by    = VALUES(updated_by),
              updated_at    = CURRENT_TIMESTAMP
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'cliente_id'    => (int)$data['cliente_id'],
            'limpeza'       => (int)$data['limpeza'],
            'data_limpeza'  => $data['data_limpeza'] ?: null,
            'cartucho'      => (int)$data['cartucho'],
            'data_cartucho' => $data['data_cartucho'] ?: null,
            'observacoes'   => $data['observacoes'] ?? null,
            'updated_by'    => $data['updated_by'] ?? null,
        ]);
    }
}