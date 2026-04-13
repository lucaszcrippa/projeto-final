<?php
include 'config/conexao.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_POST) {

    $titulo = $_POST['titulo'];
    $noticia = $_POST['noticia'];
    $autor = $_SESSION['user']['id'];

    // IMAGEM
    $img = $_FILES['imagem']['name'];
    $tmp = $_FILES['imagem']['tmp_name'];

    if ($img) {
        $img = time() . "_" . $img;
        move_uploaded_file($tmp, "img/" . $img);
    } else {
        $img = "";
    }

    // SALVAR NOTICIA
    $sql = "INSERT INTO noticias (titulo, noticia, data, autor, imagem)
            VALUES ('$titulo', '$noticia', NOW(), '$autor', '$img')";

    if (!mysqli_query($conn, $sql)) {
        die("Erro: " . mysqli_error($conn));
    }

    $id = mysqli_insert_id($conn);

    // SALVAR JOGO
    mysqli_query($conn, "INSERT INTO jogos (time_casa, time_fora, placar_casa, placar_fora, noticia_id)
    VALUES (
        '{$_POST['time_casa']}',
        '{$_POST['time_fora']}',
        '{$_POST['placar_casa']}',
        '{$_POST['placar_fora']}',
        '$id'
    )");

    echo "<p style='color:lightgreen;'>✅ Notícia publicada com sucesso!</p>";
}
?>
<link rel="stylesheet" href="css/style.css">
<div class="form-box">

<h2>📰 Nova Notícia</h2>

<a href="index.php" class="btn-voltar">⬅ Voltar</a>

<form method="POST" enctype="multipart/form-data">

<div class="input-group">
    <input type="text" name="titulo" required>
    <label>Título da notícia</label>
</div>

<div class="input-group">
    <textarea name="noticia" required></textarea>
    <label>Conteúdo da notícia</label>
</div>

<div class="upload-box">
    <p>📸 Escolha uma imagem</p>
    <input type="file" name="imagem" id="imagem" onchange="previewImagem(event)">
    <img id="preview" style="display:none;">
</div>

<h3>⚽ Dados do Jogo</h3>

<div class="grid">
    <div class="input-group">
        <input type="text" name="time_casa">
        <label>Time da casa</label>
    </div>

    <div class="input-group">
        <input type="text" name="time_fora">
        <label>Time visitante</label>
    </div>

    <div class="input-group">
        <input type="number" name="placar_casa">
        <label>Gols casa</label>
    </div>

    <div class="input-group">
        <input type="number" name="placar_fora">
        <label>Gols visitante</label>
    </div>
</div>

<button class="btn"> Publicar</button>

</form>
</div>

<script>
function previewImagem(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var img = document.getElementById("preview");
        img.src = reader.result;
        img.style.display = "block";
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>