<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/conexao.php';

// Números do resumo
$resumo = $pdo->query(
    'SELECT COUNT(*) AS total_produtos,
            COALESCE(SUM(quantidade), 0) AS total_itens,
            COALESCE(SUM(preco * quantidade), 0) AS valor_estoque,
            COALESCE(SUM(quantidade <= 5), 0) AS estoque_baixo
     FROM produtos'
)->fetch();

// Últimos 5 produtos cadastrados
$recentes = $pdo->query(
    'SELECT p.id, p.nome, p.preco, p.quantidade, c.nome AS categoria
     FROM produtos p
     LEFT JOIN categorias c ON c.id = p.categoria_id
     ORDER BY p.id DESC
     LIMIT 5'
)->fetchAll();

// 5 produtos com menos estoque (5 unidades ou menos)
$baixos = $pdo->query(
    'SELECT p.id, p.nome, p.preco, p.quantidade, c.nome AS categoria
     FROM produtos p
     LEFT JOIN categorias c ON c.id = p.categoria_id
     WHERE p.quantidade <= 5
     ORDER BY p.quantidade ASC, p.nome
     LIMIT 5'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<h1>Painel de estoque</h1>

<div class="cards">
  <div class="card">
    <span class="card-valor"><?= (int) $resumo['total_produtos'] ?></span>
    <span class="card-rotulo">Produtos cadastrados</span>
  </div>
  <div class="card">
    <span class="card-valor"><?= (int) $resumo['total_itens'] ?></span>
    <span class="card-rotulo">Itens em estoque</span>
  </div>
  <div class="card">
    <span class="card-valor">R$ <?= number_format($resumo['valor_estoque'], 2, ',', '.') ?></span>
    <span class="card-rotulo">Valor total em estoque</span>
  </div>
  <div class="card">
    <span class="card-valor <?= $resumo['estoque_baixo'] > 0 ? 'baixo' : '' ?>">
      <?= (int) $resumo['estoque_baixo'] ?>
    </span>
    <span class="card-rotulo">Com estoque baixo</span>
  </div>
</div>

<h2>Últimos produtos cadastrados</h2>
<?php if (!$recentes): ?>
  <p>Nenhum produto cadastrado ainda. <a href="/estoque/produtos/criar.php">Cadastrar o primeiro</a></p>
<?php else: ?>
  <?php $lista = $recentes; require __DIR__ . '/includes/tabela_resumo.php'; ?>
  <p><a href="/estoque/produtos/listar.php">Ver todos os produtos »</a></p>
<?php endif; ?>

<h2>Estoque baixo</h2>
<?php if (!$baixos): ?>
  <p>Nenhum produto com estoque baixo.</p>
<?php else: ?>
  <?php $lista = $baixos; require __DIR__ . '/includes/tabela_resumo.php'; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>