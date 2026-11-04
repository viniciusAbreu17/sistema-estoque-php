<?php
session_start();
require __DIR__ . '/../config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare('SELECT id, nome, senha FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header('Location: ../index.php');
        exit;
    }

    $erro = 'E-mail ou senha incorretos.';
}

require __DIR__ . '/../includes/header.php';
?>

<h1>Entrar</h1>

<?php if (isset($_GET['cadastro'])): ?>
  <p class="sucesso">Conta criada! Faça login.</p>
<?php endif; ?>

<?php if ($erro): ?>
  <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form method="POST">
  <label>E-mail <input type="email" name="email" required></label>
  <label>Senha <input type="password" name="senha" required></label>
  <button type="submit">Entrar</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>