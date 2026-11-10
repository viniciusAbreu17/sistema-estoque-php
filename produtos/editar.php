<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/conexao.php';
require __DIR__ . '/../includes/funcoes.php';

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: listar.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = ?');
$stmt->execute([$id]);
$produto = $stmt->fetch();

if (!$produto) {
    header('Location: listar.php');
    exit;
}

$categorias = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome')->fetchAll();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$produto, $erro] = validarProduto($_POST);

    if ($erro === '') {
        $stmt = $pdo->prepare(
            'UPDATE produtos
             SET nome = ?, descricao = ?, preco = ?, quantidade = ?, categoria_id = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $produto['nome'],
            $produto['descricao'],
            $produto['preco'],
            $produto['quantidade'],
            $produto['categoria_id'] !== '' ? $produto['categoria_id'] : null,
            $id,
        ]);

        header('Location: listar.php?msg=editado');
        exit;
    }
}

$titulo = 'Editar produto';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_form.php';
require __DIR__ . '/../includes/footer.php';