<?php
require __DIR__ . '/../config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = 'Esse e-mail já está cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
            $stmt->execute([$nome, $email, $hash]);
            header('Location: login.php?cadastro=ok');
            exit;
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>

<h1>Criar conta</h1>

<?php if ($erro): ?>
  <p class="erro"><?= htmlspecialchars($erro) ?></p>
<?php endif; ?>

<form method="POST">
  <label>Nome <input type="text" name="nome" required></label>
  <label>E-mail <input type="email" name="email" required></label>
  <label>Senha <input type="password" name="senha" required></label>
  <button type="submit">Cadastrar</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>