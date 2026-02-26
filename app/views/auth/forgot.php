<div class="container">
  <h1>Recuperar senha</h1>

  <?php if (!empty($_SESSION['msg'])): ?>
    <div class="alert success"><?= htmlspecialchars($_SESSION['msg']) ?></div>
    <?php unset($_SESSION['msg']); ?>
  <?php endif; ?>

  <?php if (!empty($_SESSION['debug_reset_link'])): ?>
    <div class="alert warning">
      <strong>DEV:</strong> link para resetar senha:<br>
      <a href="<?= htmlspecialchars($_SESSION['debug_reset_link']) ?>">
        <?= htmlspecialchars($_SESSION['debug_reset_link']) ?>
      </a>
    </div>
    <?php unset($_SESSION['debug_reset_link']); ?>
  <?php endif; ?>

  <form method="POST" action="/sistemaoperacionaldasbrasil/auth/sendReset">
    <label>E-mail cadastrado</label>
    <input type="email" name="email" required>

    <button class="btn" type="submit">Enviar link</button>
    <a class="link" href="/sistemaoperacionaldasbrasil/auth/login">Voltar</a>
  </form>
</div>