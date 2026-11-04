<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: auth/login.php');
    exit;
}

require 'includes/header.php';
?>

<h1>Painel de estoque</h1>
<p>Você está logado. Em breve, aqui ficam os produtos.</p>

<?php require 'includes/footer.php'; ?>