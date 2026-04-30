<?php
include 'config/conexao.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirect('index.php');
}

$stmt = mysqli_prepare($conn, "SELECT imagem FROM noticias WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$noticia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($noticia && $noticia['imagem'] && file_exists("img/" . $noticia['imagem'])) {
    unlink("img/" . $noticia['imagem']);
}

$tabelas = ['jogos', 'comentarios', 'likes'];

foreach ($tabelas as $tabela) {
    $stmtDelete = mysqli_prepare($conn, "DELETE FROM $tabela WHERE noticia_id = ?");
    mysqli_stmt_bind_param($stmtDelete, 'i', $id);
    mysqli_stmt_execute($stmtDelete);
}

$stmtNoticia = mysqli_prepare($conn, "DELETE FROM noticias WHERE id = ?");
mysqli_stmt_bind_param($stmtNoticia, 'i', $id);
mysqli_stmt_execute($stmtNoticia);

redirect('index.php');
?>
