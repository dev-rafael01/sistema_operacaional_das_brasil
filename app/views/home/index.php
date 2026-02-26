<div class="container">

  <?php if (!empty($_SESSION['success'])) : ?>
    <div style="color:green;">
      <p><?= htmlspecialchars($_SESSION['success']) ?></p>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (!empty($erros)) : ?>
    <div style="color:red;">
      <?php foreach($erros as $erro) : ?>
        <p><?= htmlspecialchars($erro) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <h1>BEM-VINDO A TELA DE LOGIN DAS BRASIL</h1>

  <!-- ✅ action explícito e sempre POST -->
  <form action="/sistemaoperacionaldasbrasil/auth/login" method="POST">
    <label>E-mail:</label>
    <input type="email" name="email" required>

    <label>Senha:</label>
    <input type="password" name="password" required>

    <!-- ✅ submit de verdade -->
    <button class="btn" type="submit">Entrar</button>

    <!-- ✅ link separado (sem button) -->
    <a class="btn btn-outline" href="/sistemaoperacionaldasbrasil/auth/registro">Cadastrar</a>
      <!-- Link esqueci minha senha -->
    <a class="link" href="/sistemaoperacionaldasbrasil/auth/forgot">Esqueci minha senha</a>
  </form>

</div>