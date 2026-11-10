<div class="tabela-wrap">
  <table class="tabela">
    <tr>
      <th>Nome</th>
      <th>Categoria</th>
      <th>Preço</th>
      <th>Qtd.</th>
    </tr>
    <?php foreach ($lista as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['nome']) ?></td>
        <td><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
        <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
        <td class="<?= $p['quantidade'] <= 5 ? 'baixo' : '' ?>">
          <?= (int) $p['quantidade'] ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>