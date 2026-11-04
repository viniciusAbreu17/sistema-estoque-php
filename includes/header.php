<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Controle de Estoque</title>
  <link rel="stylesheet" href="/estoque/assets/style.css">
</head>
<body>
<nav>
  <a href="/estoque/index.php">Início</a>
  <?php if (isset($_SESSION['usuario_id'])): ?>
    <span>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
    <a href="/estoque/auth/logout.php">Sair</a>
  <?php else: ?>
    <a href="/estoque/auth/login.php">Entrar</a>
    <a href="/estoque/auth/cadastro.php">Cadastrar</a>
  <?php endif; ?>
</nav>
<main>
    