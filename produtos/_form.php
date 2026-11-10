<h1><?= htmlspecialchars($titulo) ?></h1>

<?php if ($erro): ?>
  <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form method="POST">
  <label>Nome
    <input type="text" name="nome" value="<?= htmlspecialchars((string) $produto['nome']) ?>" required>
  </label>

  <label>Descrição
    <textarea name="descricao"><?= htmlspecialchars((string) $produto['descricao']) ?></textarea>
  </label>

  <label>Preço (R$)
    <input type="text" name="preco" value="<?= htmlspecialchars((string) $produto['preco']) ?>" required>
  </label>

  <label>Quantidade
    <input type="number" name="quantidade" min="0" value="<?= htmlspecialchars((string) $produto['quantidade']) ?>" required>
  </label>

  <label>Categoria
    <select name="categoria_id">
      <option value="">Sem categoria</option>
      <?php foreach ($categorias as $c): ?>
        <option value="<?= $c['id'] ?>"
          <?= (string) $produto['categoria_id'] === (string) $c['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['nome']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <button type="submit">Salvar</button>
  <a href="listar.php">Cancelar</a>
</form>