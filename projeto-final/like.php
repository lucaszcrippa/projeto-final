<?php
include 'config/conexao.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirect('index.php');
}

$usuarioId = (int) $_SESSION['user']['id'];
$stmt = mysqli_prepare($conn, "INSERT IGNORE INTO likes(usuario_id, noticia_id) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, 'ii', $usuarioId, $id);
mysqli_stmt_execute($stmt);

redirect('index.php');
?>
