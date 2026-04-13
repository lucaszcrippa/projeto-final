<?php 
include 'config/conexao.php';

$id = $_GET['id'];

$res = mysqli_query($conn, "SELECT imagem FROM noticias WHERE id=$id");
$d = mysqli_fetch_assoc($res);

if ($d && $d['imagem'] && file_exists("img/" . $d['imagem'])) {
    unlink("img/" . $d['imagem']);
}

mysqli_query($conn, "DELETE FROM jogos WHERE noticia_id = $id");

mysqli_query($conn, "DELETE FROM noticias WHERE id = $id");

header("Location:index.php");
?>