<div class="container">
  <h1>Definir nova senha</h1>

  <?php if (!empty($_SESSION['msg'])): ?>
    <div class="alert error"><?= htmlspecialchars($_SESSION['msg']) ?></div>
    <?php unset($_SESSION['msg']); ?>
  <?php endif; ?>

  <form method="POST" action="/sistemaoperacionaldasbrasil/auth/updatePassword">
    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

    <label>Nova senha</label>
    <input type="password" name="password" required>

    <label>Confirmar nova senha</label>
    <input type="password" name="password_confirm" required>

    <button class="btn" type="submit">Atualizar senha</button>
  </form>
</div>