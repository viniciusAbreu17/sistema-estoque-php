<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/conexao.php';

// 1. Lê os filtros que vieram pela URL (método GET)
$busca     = trim($_GET['busca'] ?? '');
$categoria = filter_var($_GET['categoria'] ?? '', FILTER_VALIDATE_INT);

$categorias = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome')->fetchAll();

// 2. Monta a consulta com as condições que foram preenchidas
$sql = 'SELECT p.id, p.nome, p.preco, p.quantidade, c.nome AS categoria
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id';

$condicoes = [];
$params    = [];

if ($busca !== '') {
    $condicoes[] = 'p.nome LIKE ?';
    $params[]    = '%' . $busca . '%';
}

if ($categoria) {
    $condicoes[] = 'p.categoria_id = ?';
    $params[]    = $categoria;
}

if ($condicoes) {
    $sql .= ' WHERE ' . implode(' AND ', $condicoes);
}

$sql .= ' ORDER BY p.nome';

// 3. Executa com prepared statement
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();

$filtrando = ($busca !== '' || $categoria);

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

<form method="GET" class="filtros">
  <input type="text" name="busca" placeholder="Buscar por nome"
         value="<?= htmlspecialchars($busca) ?>">

  <select name="categoria">
    <option value="">Todas as categorias</option>
    <?php foreach ($categorias as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $categoria === (int) $c['id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($c['nome']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <button type="submit">Filtrar</button>

  <?php if ($filtrando): ?>
    <a href="listar.php">Limpar filtros</a>
  <?php endif; ?>
</form>

<p><a href="criar.php">+ Novo produto</a></p>

<?php if (!$produtos): ?>
  <p>
    <?= $filtrando
        ? 'Nenhum produto encontrado com esses filtros.'
        : 'Nenhum produto cadastrado ainda.' ?>
  </p>
<?php else: ?>
  <p><?= count($produtos) ?> produto(s) encontrado(s).</p>

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