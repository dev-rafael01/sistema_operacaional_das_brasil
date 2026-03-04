<div class="container">
  <h1><?= htmlspecialchars($cliente['nome']) ?></h1>

  <p class="muted">
    Última atualização:
    <strong>
      <?= $checklist && !empty($checklist['updated_at'])
          ? htmlspecialchars($checklist['updated_at'])
          : 'Ainda não tem' ?>
    </strong>
  </p>

  <div class="hr"></div>

  <h2>Resumo</h2>

  <?php
    // helpers simples
    $simNao = function($v) {
      return ((int)$v === 1) ? 'Sim' : 'Não';
    };

    $mostraDataSeSim = function($flag, $data) {
      if ((int)$flag === 1 && !empty($data)) {
        return ' (' . htmlspecialchars($data) . ')';
      }
      return '';
    };

    $mostraQtdSeSim = function($flag, $qtd) {
      if ((int)$flag === 1 && $qtd !== null && $qtd !== '') {
        return ' — <strong>Qtd:</strong> ' . htmlspecialchars((string)$qtd);
      }
      return '';
    };
  ?>

  <p>
    1) Limpeza no tanque intermediário?
    <strong><?= $checklist ? $simNao($checklist['limpeza_tanque_intermediario'] ?? 0) : 'Não' ?></strong>
    <?= $checklist ? $mostraDataSeSim($checklist['limpeza_tanque_intermediario'] ?? 0, $checklist['data_limpeza_tanque_intermediario'] ?? '') : '' ?>
  </p>

  <p>
    2) Limpeza no sedimentador?
    <strong><?= $checklist ? $simNao($checklist['limpeza_sedimentador'] ?? 0) : 'Não' ?></strong>
    <?= $checklist ? $mostraDataSeSim($checklist['limpeza_sedimentador'] ?? 0, $checklist['data_limpeza_sedimentador'] ?? '') : '' ?>
  </p>

  <p>
    3) Troca de cartucho/begues do sistema terciário?
    <strong><?= $checklist ? $simNao($checklist['troca_cartucho_terciario'] ?? 0) : 'Não' ?></strong>
    <?= $checklist ? $mostraDataSeSim($checklist['troca_cartucho_terciario'] ?? 0, $checklist['data_troca_cartucho_terciario'] ?? '') : '' ?>
  </p>

  <p>
    4) Qual quantidade de cloro tem no local?
    <strong><?= $checklist ? $simNao($checklist['cloro_ok'] ?? 0) : 'Não' ?></strong>
    <?php if ($checklist && (int)($checklist['cloro_ok'] ?? 0) === 1 && !empty($checklist['quantidade_cloro'])): ?>
      — <strong>Qtd:</strong> <?= htmlspecialchars($checklist['quantidade_cloro']) ?>
    <?php endif; ?>
  </p>

  <p>
    5) Tem begues ou cartucho no local?
    <strong><?= $checklist ? $simNao($checklist['tem_begues_cartucho'] ?? 0) : 'Não' ?></strong>
    <?= $checklist ? $mostraQtdSeSim($checklist['tem_begues_cartucho'] ?? 0, $checklist['quantidade_begues_cartucho'] ?? '') : '' ?>
  </p>

  <?php if ($checklist && !empty($checklist['observacoes'])): ?>
    <p>Obs: <?= nl2br(htmlspecialchars($checklist['observacoes'])) ?></p>
  <?php endif; ?>

  <div class="hr"></div>

  <h2>Checklist</h2>

  <?php $podeEditar = !empty($_SESSION['usuario']['is_admin']); ?>

  <?php if (!$podeEditar): ?>
    <div class="alert warning">
      Você tem acesso somente à visualização. Não pode salvar/editar.
    </div>
  <?php endif; ?>

  <form method="POST" action="/sistemaoperacionaldasbrasil/checklist/save" enctype="multipart/form-data">
    <input type="hidden" name="cliente_id" value="<?= (int)$cliente['id'] ?>">

    <!-- 1 -->
    <label>1) Limpeza no tanque intermediário?</label>
    <select name="limpeza_tanque_intermediario" <?= !$podeEditar ? 'disabled' : '' ?>>
      <option value="0" <?= (!$checklist || (int)($checklist['limpeza_tanque_intermediario'] ?? 0) === 0) ? 'selected' : '' ?>>Não</option>
      <option value="1" <?= ($checklist && (int)($checklist['limpeza_tanque_intermediario'] ?? 0) === 1) ? 'selected' : '' ?>>Sim</option>
    </select>

    <label>Se sim, informe a data</label>
    <input type="date" name="data_limpeza_tanque_intermediario"
      value="<?= $checklist ? htmlspecialchars($checklist['data_limpeza_tanque_intermediario'] ?? '') : '' ?>"
      <?= !$podeEditar ? 'disabled' : '' ?>>

    <!-- 2 -->
    <label>2) Limpeza no sedimentador?</label>
    <select name="limpeza_sedimentador" <?= !$podeEditar ? 'disabled' : '' ?>>
      <option value="0" <?= (!$checklist || (int)($checklist['limpeza_sedimentador'] ?? 0) === 0) ? 'selected' : '' ?>>Não</option>
      <option value="1" <?= ($checklist && (int)($checklist['limpeza_sedimentador'] ?? 0) === 1) ? 'selected' : '' ?>>Sim</option>
    </select>

    <label>Se sim, informe a data</label>
    <input type="date" name="data_limpeza_sedimentador"
      value="<?= $checklist ? htmlspecialchars($checklist['data_limpeza_sedimentador'] ?? '') : '' ?>"
      <?= !$podeEditar ? 'disabled' : '' ?>>

    <!-- 3 -->
    <label>3) Troca de cartucho/begues do sistema terciário?</label>
    <select name="troca_cartucho_terciario" <?= !$podeEditar ? 'disabled' : '' ?>>
      <option value="0" <?= (!$checklist || (int)($checklist['troca_cartucho_terciario'] ?? 0) === 0) ? 'selected' : '' ?>>Não</option>
      <option value="1" <?= ($checklist && (int)($checklist['troca_cartucho_terciario'] ?? 0) === 1) ? 'selected' : '' ?>>Sim</option>
    </select>

    <label>Se sim, informe a data</label>
    <input type="date" name="data_troca_cartucho_terciario"
      value="<?= $checklist ? htmlspecialchars($checklist['data_troca_cartucho_terciario'] ?? '') : '' ?>"
      <?= !$podeEditar ? 'disabled' : '' ?>>

    <!-- 4 -->
    <label>4) Te cloro no local??</label>
    <select name="cloro_ok" <?= !$podeEditar ? 'disabled' : '' ?>>
      <option value="0" <?= (!$checklist || (int)($checklist['cloro_ok'] ?? 0) === 0) ? 'selected' : '' ?>>Não</option>
      <option value="1" <?= ($checklist && (int)($checklist['cloro_ok'] ?? 0) === 1) ? 'selected' : '' ?>>Sim</option>
    </select>

    <label>Se sim, informe a quantidade (ex: 2 kg / 10 L / 1 galão)</label>
    <input type="text" name="quantidade_cloro"
      value="<?= $checklist ? htmlspecialchars($checklist['quantidade_cloro'] ?? '') : '' ?>"
      <?= !$podeEditar ? 'disabled' : '' ?>>

    <!-- 5 -->
    <label>5) Tem begues ou cartucho no local?</label>
    <select name="tem_begues_cartucho" <?= !$podeEditar ? 'disabled' : '' ?>>
      <option value="0" <?= (!$checklist || (int)($checklist['tem_begues_cartucho'] ?? 0) === 0) ? 'selected' : '' ?>>Não</option>
      <option value="1" <?= ($checklist && (int)($checklist['tem_begues_cartucho'] ?? 0) === 1) ? 'selected' : '' ?>>Sim</option>
    </select>

    <label>Se sim, informe a quantidade</label>
    <input type="number" min="0" step="1" name="quantidade_begues_cartucho"
      value="<?= $checklist ? htmlspecialchars((string)($checklist['quantidade_begues_cartucho'] ?? '')) : '' ?>"
      <?= !$podeEditar ? 'disabled' : '' ?>>

    <label>Observações</label>
    <input type="text" name="observacoes"
      value="<?= $checklist ? htmlspecialchars($checklist['observacoes'] ?? '') : '' ?>"
      <?= !$podeEditar ? 'disabled' : '' ?>>

    <div class="hr"></div>

    <h2>Vídeos (até 3 min cada)</h2>

  <!-- ================= PAINEL ELETRICO ================= -->
  <label>Painel elétrico</label>

  <?php if (!empty($checklist['video_painel_eletrico'])): ?>
    <video controls style="width:100%;border-radius:12px;margin-bottom:10px;">
      <source src="<?= htmlspecialchars($checklist['video_painel_eletrico']) ?>" type="video/mp4">
    </video>
  <?php endif; ?>

  <input type="file" name="video_painel_eletrico" <?= !$podeEditar ? 'disabled' : '' ?>>

  <?php if (!empty($checklist['video_painel_eletrico']) && $podeEditar): ?>
    <button type="submit"
            name="delete_video"
            value="video_painel_eletrico"
            class="btn btn-outline"
            style="background:#ffdddd;color:#a00;border-color:#a00;margin-bottom:15px;"
            onclick="return confirm('Deseja realmente excluir este vídeo?')">
        Excluir vídeo
    </button>
  <?php endif; ?>


  <!-- ================= SISTEMA PRIMARIO ================= -->
  <label>Sistema primário</label>

  <?php if (!empty($checklist['video_sistema_primario'])): ?>
    <video controls style="width:100%;border-radius:12px;margin-bottom:10px;">
      <source src="<?= htmlspecialchars($checklist['video_sistema_primario']) ?>" type="video/mp4">
    </video>
  <?php endif; ?>

  <input type="file" name="video_sistema_primario" <?= !$podeEditar ? 'disabled' : '' ?>>

  <?php if (!empty($checklist['video_sistema_primario']) && $podeEditar): ?>
    <button type="submit"
            name="delete_video"
            value="video_sistema_primario"
            class="btn btn-outline"
            style="background:#ffdddd;color:#a00;border-color:#a00;margin-bottom:15px;"
            onclick="return confirm('Deseja realmente excluir este vídeo?')">
        Excluir vídeo
    </button>
  <?php endif; ?>


  <!-- ================= SISTEMA SECUNDARIO ================= -->
  <label>Sistema secundário</label>

  <?php if (!empty($checklist['video_sistema_secundario'])): ?>
    <video controls style="width:100%;border-radius:12px;margin-bottom:10px;">
      <source src="<?= htmlspecialchars($checklist['video_sistema_secundario']) ?>" type="video/mp4">
    </video>
  <?php endif; ?>

  <input type="file" name="video_sistema_secundario" <?= !$podeEditar ? 'disabled' : '' ?>>

  <?php if (!empty($checklist['video_sistema_secundario']) && $podeEditar): ?>
    <button type="submit"
            name="delete_video"
            value="video_sistema_secundario"
            class="btn btn-outline"
            style="background:#ffdddd;color:#a00;border-color:#a00;margin-bottom:15px;"
            onclick="return confirm('Deseja realmente excluir este vídeo?')">
        Excluir vídeo
    </button>
  <?php endif; ?>


  <!-- ================= SISTEMA TERCIARIO ================= -->
  <label>Sistema terciário</label>

  <?php if (!empty($checklist['video_sistema_terciario'])): ?>
    <video controls style="width:100%;border-radius:12px;margin-bottom:10px;">
      <source src="<?= htmlspecialchars($checklist['video_sistema_terciario']) ?>" type="video/mp4">
    </video>
  <?php endif; ?>

  <input type="file" name="video_sistema_terciario" <?= !$podeEditar ? 'disabled' : '' ?>>

    <?php if (!empty($checklist['video_sistema_terciario']) && $podeEditar): ?>
      <button type="submit"
            name="delete_video"
            value="video_sistema_terciario"
            class="btn btn-outline"
            style="background:#ffdddd;color:#a00;border-color:#a00;margin-bottom:15px;"
            onclick="return confirm('Deseja realmente excluir este vídeo?')">
        Excluir vídeo
      </button>
    <?php endif; ?>

      <?php if ($podeEditar): ?>
        <button class="btn" type="submit">Salvar</button>
      <?php endif; ?>
    </form>

  <div class="actions">
    <a class="btn btn-outline" href="/sistemaoperacionaldasbrasil/cliente/index">Voltar para clientes</a>
    <a class="btn btn-outline" href="/sistemaoperacionaldasbrasil/home/logado">Home</a>
  </div>
</div>