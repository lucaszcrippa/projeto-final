<link rel="stylesheet" href="css/style.css">

<?php 
include 'config/conexao.php';

$id = $_GET['id'];

// Buscar dados atuais
$d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM noticias WHERE id=$id"));
?>

<form method="POST" enctype="multipart/form-data">

<input name="titulo" value="<?php echo $d['titulo']; ?>">

<textarea name="noticia"><?php echo $d['noticia']; ?></textarea>

<!-- IMAGEM ATUAL -->
<p>Imagem atual:</p>
<img src="img/<?php echo $d['imagem']; ?>" width="150"><br><br>

<!-- NOVA IMAGEM -->
<input type="file" name="imagem"><br><br>

<button>Salvar</button>

</form>

<?php
if ($_POST) {

    $titulo = $_POST['titulo'];
    $noticia = $_POST['noticia'];

    // IMAGEM NOVA
    $img = $_FILES['imagem']['name'];
    $tmp = $_FILES['imagem']['tmp_name'];

    // Se enviou nova imagem
    if ($img) {

        $nova_img = time() . "_" . $img;
        move_uploaded_file($tmp, "img/" . $nova_img);

        // Apagar imagem antiga
        if ($d['imagem'] && file_exists("img/" . $d['imagem'])) {
            unlink("img/" . $d['imagem']);
        }

    } else {
        // Mantém a antiga
        $nova_img = $d['imagem'];
    }

    // UPDATE
    mysqli_query($conn, "UPDATE noticias SET 
        titulo='$titulo',
        noticia='$noticia',
        imagem='$nova_img'
        WHERE id=$id
    ");

    header("Location:index.php");
}
?>