<?php include 'config/conexao.php'; ?>
<link rel="stylesheet" href="css/style.css">

<?php $id=$_GET['id'];
$d=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM noticias WHERE id=$id"));
?>

<h1><?php echo $d['titulo']; ?></h1>
<img src="img/<?php echo $d['imagem']; ?>" width="100%">
<p><?php echo $d['noticia']; ?></p>

<form method="POST">
<textarea name="comentario"></textarea>
<button>Comentar</button>
</form>

<?php
if($_POST){
mysqli_query($conn,"INSERT INTO comentarios(texto,usuario_id,noticia_id,data)
VALUES('{$_POST['comentario']}','{$_SESSION['user']['id']}','$id',NOW())");
}

$sql=mysqli_query($conn,"SELECT c.*,u.nome FROM comentarios c
JOIN usuarios u ON u.id=c.usuario_id WHERE noticia_id=$id");

while($c=mysqli_fetch_assoc($sql)){
echo "<p><b>{$c['nome']}:</b> {$c['texto']}</p>";
}
?>
