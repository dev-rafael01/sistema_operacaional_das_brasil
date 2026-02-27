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
        // Antigos
        if ((int)$data['limpeza'] !== 1) $data['data_limpeza'] = null;
        if ((int)$data['cartucho'] !== 1) $data['data_cartucho'] = null;

        // Novos: datas só se SIM
        if ((int)$data['limpeza_tanque_intermediario'] !== 1) $data['data_limpeza_tanque_intermediario'] = null;
        if ((int)$data['limpeza_sedimentador'] !== 1) $data['data_limpeza_sedimentador'] = null;
        if ((int)$data['troca_cartucho_terciario'] !== 1) $data['data_troca_cartucho_terciario'] = null;

        // Quantidades só se SIM
        if ((int)$data['cloro_ok'] !== 1) $data['quantidade_cloro'] = null;
        if ((int)$data['tem_begues_cartucho'] !== 1) $data['quantidade_begues_cartucho'] = null;

        $sql = "
            INSERT INTO checklist
            (
              cliente_id,
              limpeza, data_limpeza,
              cartucho, data_cartucho,
              observacoes,
              limpeza_tanque_intermediario, data_limpeza_tanque_intermediario,
              limpeza_sedimentador, data_limpeza_sedimentador,
              troca_cartucho_terciario, data_troca_cartucho_terciario,
              cloro_ok, quantidade_cloro,
              tem_begues_cartucho, quantidade_begues_cartucho,
              video_painel_eletrico, video_sistema_primario, video_sistema_secundario, video_sistema_terciario,
              updated_by
            )
            VALUES
            (
              :cliente_id,
              :limpeza, :data_limpeza,
              :cartucho, :data_cartucho,
              :observacoes,
              :limpeza_tanque_intermediario, :data_limpeza_tanque_intermediario,
              :limpeza_sedimentador, :data_limpeza_sedimentador,
              :troca_cartucho_terciario, :data_troca_cartucho_terciario,
              :cloro_ok, :quantidade_cloro,
              :tem_begues_cartucho, :quantidade_begues_cartucho,
              :video_painel_eletrico, :video_sistema_primario, :video_sistema_secundario, :video_sistema_terciario,
              :updated_by
            )
            ON DUPLICATE KEY UPDATE
              limpeza = VALUES(limpeza),
              data_limpeza = VALUES(data_limpeza),
              cartucho = VALUES(cartucho),
              data_cartucho = VALUES(data_cartucho),
              observacoes = VALUES(observacoes),

              limpeza_tanque_intermediario = VALUES(limpeza_tanque_intermediario),
              data_limpeza_tanque_intermediario = VALUES(data_limpeza_tanque_intermediario),

              limpeza_sedimentador = VALUES(limpeza_sedimentador),
              data_limpeza_sedimentador = VALUES(data_limpeza_sedimentador),

              troca_cartucho_terciario = VALUES(troca_cartucho_terciario),
              data_troca_cartucho_terciario = VALUES(data_troca_cartucho_terciario),

              cloro_ok = VALUES(cloro_ok),
              quantidade_cloro = VALUES(quantidade_cloro),

              tem_begues_cartucho = VALUES(tem_begues_cartucho),
              quantidade_begues_cartucho = VALUES(quantidade_begues_cartucho),

              video_painel_eletrico = VALUES(video_painel_eletrico),
              video_sistema_primario = VALUES(video_sistema_primario),
              video_sistema_secundario = VALUES(video_sistema_secundario),
              video_sistema_terciario = VALUES(video_sistema_terciario),

              updated_by = VALUES(updated_by),
              updated_at = CURRENT_TIMESTAMP
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'cliente_id' => (int)$data['cliente_id'],

            'limpeza' => (int)$data['limpeza'],
            'data_limpeza' => $data['data_limpeza'] ?: null,

            'cartucho' => (int)$data['cartucho'],
            'data_cartucho' => $data['data_cartucho'] ?: null,

            'observacoes' => $data['observacoes'] ?? null,

            'limpeza_tanque_intermediario' => (int)$data['limpeza_tanque_intermediario'],
            'data_limpeza_tanque_intermediario' => $data['data_limpeza_tanque_intermediario'] ?: null,

            'limpeza_sedimentador' => (int)$data['limpeza_sedimentador'],
            'data_limpeza_sedimentador' => $data['data_limpeza_sedimentador'] ?: null,

            'troca_cartucho_terciario' => (int)$data['troca_cartucho_terciario'],
            'data_troca_cartucho_terciario' => $data['data_troca_cartucho_terciario'] ?: null,

            'cloro_ok' => (int)$data['cloro_ok'],
            'quantidade_cloro' => $data['quantidade_cloro'] ?: null,

            'tem_begues_cartucho' => (int)$data['tem_begues_cartucho'],
            'quantidade_begues_cartucho' => ($data['quantidade_begues_cartucho'] !== '' ? (int)$data['quantidade_begues_cartucho'] : null),

            'video_painel_eletrico' => $data['video_painel_eletrico'] ?? null,
            'video_sistema_primario' => $data['video_sistema_primario'] ?? null,
            'video_sistema_secundario' => $data['video_sistema_secundario'] ?? null,
            'video_sistema_terciario' => $data['video_sistema_terciario'] ?? null,

            'updated_by' => $data['updated_by'] ?? null,
        ]);
    }
}