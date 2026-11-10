<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/conexao.php';
require __DIR__ . '/../includes/funcoes.php';

$porPagina = 10;

// 1. Lê filtros e página atual da URL (GET)
$busca     = trim($_GET['busca'] ?? '');
$categoria = filter_var($_GET['categoria'] ?? '', FILTER_VALIDATE_INT);
$pagina    = max(1, (int) filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT));

$categorias = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome')->fetchAll();

// 2. Monta as condições do WHERE
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

$where = $condicoes ? ' WHERE ' . implode(' AND ', $condicoes) : '';

// 3. Conta o total de produtos que batem com os filtros
$stmtTotal = $pdo->prepare('SELECT COUNT(*) FROM produtos p' . $where);
$stmtTotal->execute($params);
$total = (int) $stmtTotal->fetchColumn();

$totalPaginas = max(1, (int) ceil($total / $porPagina));
$pagina       = min($pagina, $totalPaginas);
$offset       = ($pagina - 1) * $porPagina;

// 4. Busca só os produtos da página atual
$sql = 'SELECT p.id, p.nome, p.preco, p.quantidade, c.nome AS categoria
        FROM produtos p
        LEFT JOIN categorias c ON c.id = p.categoria_id'
     . $where
     . ' ORDER BY p.nome'
     . ' LIMIT ' . $porPagina . ' OFFSET ' . $offset;

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

<p><a href="criar.php" class="btn-novo">+ Novo produto</a></p>

<?php if (!$produtos): ?>
  <p>
    <?= $filtrando
        ? 'Nenhum produto encontrado com esses filtros.'
        : 'Nenhum produto cadastrado ainda.' ?>
  </p>
<?php else: ?>
  <p class="resumo">
    <?= $total ?> produto(s) encontrado(s) — página <?= $pagina ?> de <?= $totalPaginas ?>
  </p>

  <div class="tabela-wrap">
    <table class="tabela">
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
          <td class="<?= $p['quantidade'] <= 5 ? 'baixo' : '' ?>">
            <?= (int) $p['quantidade'] ?>
          </td>
          <td>
            <div class="acoes">
              <a href="editar.php?id=<?= (int) $p['id'] ?>">Editar</a>
              <form method="POST" action="excluir.php"
                    onsubmit="return confirm('Excluir este produto?');">
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <button type="submit" class="btn-perigo">Excluir</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <?php if ($totalPaginas > 1): ?>
    <div class="paginacao">
      <?php if ($pagina > 1): ?>
        <a href="<?= htmlspecialchars(urlPagina($pagina - 1, $busca, $categoria)) ?>">« Anterior</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <?php if ($i === $pagina): ?>
          <span class="atual"><?= $i ?></span>
        <?php else: ?>
          <a href="<?= htmlspecialchars(urlPagina($i, $busca, $categoria)) ?>"><?= $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>

      <?php if ($pagina < $totalPaginas): ?>
        <a href="<?= htmlspecialchars(urlPagina($pagina + 1, $busca, $categoria)) ?>">Próxima »</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>