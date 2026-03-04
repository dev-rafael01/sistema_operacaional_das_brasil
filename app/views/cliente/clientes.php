<?php if (!empty($_SESSION['usuario']['is_admin'])): ?>
    <a class="btn" style="margin-bottom:15px;"
       href="/sistemaoperacionaldasbrasil/cliente/create">
        + Novo Local
    </a>
<?php endif; ?>

<?php foreach ($clientes as $c): ?>
  <div class="card">

      <a href="/sistemaoperacionaldasbrasil/cliente/show?id=<?= (int)$c['id'] ?>">
          <h2><?= htmlspecialchars($c['nome']) ?></h2>
      </a>

      <?php if (!empty($_SESSION['usuario']['is_admin'])): ?>
          <div style="margin-top:10px;">
              <a href="/sistemaoperacionaldasbrasil/cliente/delete?id=<?= (int)$c['id'] ?>"
                 onclick="return confirm('Tem certeza que deseja excluir este local? Isso apagará checklist e vídeos.')"
                 style="color:#a00;font-weight:bold;text-decoration:none;">
                  🗑 Excluir
              </a>
          </div>
      <?php endif; ?>
  </div>
      
<?php endforeach; ?>
    <div style="text-align:center; margin-top:30px;">
    <a class="btn-voltar"
       href="/sistemaoperacionaldasbrasil/home/logado">
        Voltar
    </a>
</div>
</div>