<div class="container">
  <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']['nome_completo'] ?? '') ?></h1>

  <p class="muted">
    Cargo:
    <span class="badge"><?= htmlspecialchars($_SESSION['usuario']['cargo'] ?? '') ?></span>
  </p>

  <div class="actions">
    <a class="btn" href="/sistemaoperacionaldasbrasil/cliente/index">Clientes</a>
    <a class="btn btn-outline" href="/sistemaoperacionaldasbrasil/auth/logout">Sair</a>
  </div>
</div>