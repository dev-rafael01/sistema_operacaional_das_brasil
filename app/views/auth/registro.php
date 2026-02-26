<div class="container">
 
    <?php if(!empty($erros)) :  ?>
            <div style="color: red;">
                <?php foreach ($erros as $erro) : ?>
                    <p><?= $erro ?></p>
                    <?php endforeach;  ?>
            </div>
        
        <?php endif;  ?>
    <!-- ///////////////////////////////////////////////////////////////////////////////////////-->

        
        <h1>Cadastro de Usuário</h1>

        <form method="POST">

            <label>Nome:</label>
            <input type="text" name="name">

            <label>Sobrenome:</label>
            <input type="text" name="sobrenome">

            <label>Email:</label>
            <input type="email" name="email">

            <label>Telefone</label>
            <input type="text" name="telefone">

            <label for="cargo">Escolha seu cargo:</label>
            <select id="cargo" name="cargo">
            <option value="">Selecione um cargo</option>
            <option value="Operador Junior">Operador Junior</option>
            <option value="Operador Pleno">Operador Pleno</option>
            <option value="Operador Senior">Operador Senior</option>
            <option value="Supervisor de Operação">Supervisor de Operação</option>
            <option value="Engenheiro">Engenheiro</option>
            <option value="Comercial">Comercial</option>
            <option value="Recursos Humanos">Recursos Humanos</option>
            <option value="Pos Venda">Pos Venda</option>
            <option value="Compras">Compras</option>
            <option value="Ceo">Ceo</option>
            <option value="Outro">Outro</option>
            </select>

            <label>Senha:</label>
            <input type="password" name="password">

            <button class="btn" type="submit">Cadastrar</button>
        </form>
</div>

