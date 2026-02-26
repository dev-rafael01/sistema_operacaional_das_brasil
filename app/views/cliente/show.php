<div class="container">
  <h1><?= htmlspecialchars($cliente['nome']) ?></h1>

  <p class="muted">
    Última atualização:
    <strong><?= $checklist ? htmlspecialchars($checklist['updated_at']) : 'Ainda não tem' ?></strong>
  </p>

  <div class="hr"></div>

  <h2>Resumo</h2>

  <p>Limpeza: <strong><?= ($checklist && (int)$checklist['limpeza'] === 1) ? 'Sim' : 'Não' ?></strong>
    <?php if ($checklist && (int)$checklist['limpeza'] === 1 && !empty($checklist['data_limpeza'])): ?>
      (<?= htmlspecialchars($checklist['data_limpeza']) ?>)
    <?php endif; ?>
  </p>

  <p>Cartucho: <strong><?= ($checklist && (int)$checklist['cartucho'] === 1) ? 'Sim' : 'Não' ?></strong>
    <?php if ($checklist && (int)$checklist['cartucho'] === 1 && !empty($checklist['data_cartucho'])): ?>
      (<?= htmlspecialchars($checklist['data_cartucho']) ?>)
    <?php endif; ?>
  </p>

  <?php if ($checklist && !empty($checklist['observacoes'])): ?>
    <p>Obs: <?= nl2br(htmlspecialchars($checklist['observacoes'])) ?></p>
  <?php endif; ?>

  <div class="hr"></div>
  <h2>Checklist</h2>

  <?php if (empty($_SESSION['usuario']['is_admin'])): ?>
    <div class="alert warning">
      Você tem acesso somente à visualização. Não pode salvar/editar.
    </div>
  <?php endif; ?>

  <form method="POST" action="/sistemaoperacionaldasbrasil/checklist/save">
    <input type="hidden" name="cliente_id" value="<?= (int)$cliente['id'] ?>">

    <label>Foi feita limpeza?</label>
    <select name="limpeza" <?= empty($_SESSION['usuario']['is_admin']) ? 'disabled' : '' ?>>
      <option value="0" <?= (!$checklist || (int)$checklist['limpeza'] === 0) ? 'selected' : '' ?>>Não</option>
      <option value="1" <?= ($checklist && (int)$checklist['limpeza'] === 1) ? 'selected' : '' ?>>Sim</option>
    </select>

    <label>Se sim, informe a data</label>
    <input type="date" name="data_limpeza"
      value="<?= $checklist ? htmlspecialchars($checklist['data_limpeza'] ?? '') : '' ?>"
      <?= empty($_SESSION['usuario']['is_admin']) ? 'disabled' : '' ?>>

    <label>Trocou cartucho?</label>
    <select name="cartucho" <?= empty($_SESSION['usuario']['is_admin']) ? 'disabled' : '' ?>>
      <option value="0" <?= (!$checklist || (int)$checklist['cartucho'] === 0) ? 'selected' : '' ?>>Não</option>
      <option value="1" <?= ($checklist && (int)$checklist['cartucho'] === 1) ? 'selected' : '' ?>>Sim</option>
    </select>

    <label>Se sim, informe a data</label>
    <input type="date" name="data_cartucho"
      value="<?= $checklist ? htmlspecialchars($checklist['data_cartucho'] ?? '') : '' ?>"
      <?= empty($_SESSION['usuario']['is_admin']) ? 'disabled' : '' ?>>

    <label>Observações</label>
    <input type="text" name="observacoes"
      value="<?= $checklist ? htmlspecialchars($checklist['observacoes'] ?? '') : '' ?>"
      <?= empty($_SESSION['usuario']['is_admin']) ? 'disabled' : '' ?>>

    <?php if (!empty($_SESSION['usuario']['is_admin'])): ?>
      <button class="btn" type="submit">Salvar</button>
    <?php endif; ?>
  </form>

  <div class="actions">
    <a class="btn btn-outline" href="/sistemaoperacionaldasbrasil/cliente/index">Voltar para clientes</a>
    <a class="btn btn-outline" href="/sistemaoperacionaldasbrasil/home/logado">Tela inicial</a>
  </div>
</div>