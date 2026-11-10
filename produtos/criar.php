<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/conexao.php';
require __DIR__ . '/../includes/funcoes.php';

$categorias = $pdo->query('SELECT id, nome FROM categorias ORDER BY nome')->fetchAll();

$produto = [
    'nome' => '', 'descricao' => '', 'preco' => '',
    'quantidade' => '0', 'categoria_id' => '',
];
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$produto, $erro] = validarProduto($_POST);

    if ($erro === '') {
        $stmt = $pdo->prepare(
            'INSERT INTO produtos (nome, descricao, preco, quantidade, categoria_id)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $produto['nome'],
            $produto['descricao'],
            $produto['preco'],
            $produto['quantidade'],
            $produto['categoria_id'] !== '' ? $produto['categoria_id'] : null,
        ]);

        header('Location: listar.php?msg=criado');
        exit;
    }
}

$titulo = 'Novo produto';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_form.php';
require __DIR__ . '/../includes/footer.php';