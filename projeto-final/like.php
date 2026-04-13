<?php include 'config/conexao.php';
mysqli_query($conn,"INSERT INTO likes(usuario_id,noticia_id)
VALUES('{$_SESSION['user']['id']}','{$_GET['id']}')");
header("Location:index.php");
?>