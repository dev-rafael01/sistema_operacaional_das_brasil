<div class="container">
  <h1>Clientes</h1>

  <div class="grid">
    <?php foreach ($clientes as $c): ?>
      <a class="card" href="/sistemaoperacionaldasbrasil/cliente/show?id=<?= (int)$c['id'] ?>">
        <h2><?= htmlspecialchars($c['nome']) ?></h2>
        <p class="muted">Clique para ver/atualizar checklist</p>
      </a>
    <?php endforeach; ?>
  </div>

  <a class="link" href="/sistemaoperacionaldasbrasil/home/logado">Voltar</a>
</div>