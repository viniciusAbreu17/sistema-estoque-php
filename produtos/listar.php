<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/conexao.php';

$sql = 'SELECT p.id, p.nome, p.preco, p.quantidade, c.nome AS categoria
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        ORDER BY p.nome';
$produtos = $pdo->query($sql)->fetchAll();

$mensagens = [
    'criado'   => 'Produto cadastrado!',
    'editado'  => 'Produto atualizado!',
    'excluido' => 'Produto excluído!',
];
$msg = $mensagens[$_GET['msg'] ?? ''] ?? '';

require __DIR__ . '/../includes/header.php';
?>

<h1>Produtos</h1>

<?php if ($msg): ?>
  <p class="sucesso"><?= $msg ?></p>
<?php endif; ?>

<p><a href="criar.php">+ Novo produto</a></p>

<?php if (!$produtos): ?>
  <p>Nenhum produto cadastrado ainda.</p>
<?php else: ?>
  <table border="1" cellpadding="6">
    <tr>
      <th>Nome</th>
      <th>Categoria</th>
      <th>Preço</th>
      <th>Qtd.</th>
      <th>Ações</th>
    </tr>
    <?php foreach ($produtos as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['nome']) ?></td>
        <td><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
        <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
        <td><?= (int) $p['quantidade'] ?></td>
        <td>
          <a href="editar.php?id=<?= (int) $p['id'] ?>">Editar</a>
          <form method="POST" action="excluir.php" style="display:inline"
                onsubmit="return confirm('Excluir este produto?');">
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <button type="submit">Excluir</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>