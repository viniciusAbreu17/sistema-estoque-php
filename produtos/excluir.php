<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = ?');
        $stmt->execute([$id]);
    }
}

header('Location: listar.php?msg=excluido');
exit;