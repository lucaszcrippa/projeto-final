<?php
include 'config/conexao.php';
require_login();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $noticia = trim($_POST['noticia'] ?? '');
    $autor = (int) $_SESSION['user']['id'];
    $imagem = salvar_imagem_upload('imagem');

    if ($titulo === '' || $noticia === '') {
        $erro = 'Preencha o título e o conteúdo da notícia.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO noticias (titulo, noticia, data, autor, imagem) VALUES (?, ?, NOW(), ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssis', $titulo, $noticia, $autor, $imagem);
        mysqli_stmt_execute($stmt);

        $id = mysqli_insert_id($conn);
        $timeCasa = trim($_POST['time_casa'] ?? '');
        $timeFora = trim($_POST['time_fora'] ?? '');
        $placarCasa = ($_POST['placar_casa'] === '') ? null : (int) $_POST['placar_casa'];
        $placarFora = ($_POST['placar_fora'] === '') ? null : (int) $_POST['placar_fora'];

        if ($timeCasa !== '' || $timeFora !== '') {
            $stmtJogo = mysqli_prepare($conn, "INSERT INTO jogos (time_casa, time_fora, placar_casa, placar_fora, noticia_id) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtJogo, 'ssiii', $timeCasa, $timeFora, $placarCasa, $placarFora, $id);
            mysqli_stmt_execute($stmtJogo);
        }

        redirect('index.php');
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nova notícia | Arena Esportiva</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="form-page">
        <section class="form-box">
            <a href="index.php" class="btn-voltar">Voltar</a>
            <p class="eyebrow">Publicação</p>
            <h1>Nova notícia</h1>

            <?php if ($erro) { ?>
                <p class="alert"><?php echo e($erro); ?></p>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <input type="text" name="titulo" placeholder=" " required>
                    <label>Título da notícia</label>
                </div>

                <div class="input-group">
                    <textarea name="noticia" placeholder=" " rows="8" required></textarea>
                    <label>Conteúdo da notícia</label>
                </div>

                <div class="upload-box">
                    <label for="imagem">Imagem da matéria</label>
                    <input type="file" name="imagem" id="imagem" accept="image/png,image/jpeg,image/webp" onchange="previewImagem(event)">
                    <img id="preview" alt="" style="display:none;">
                </div>

                <h2>Dados do jogo</h2>
                <div class="grid">
                    <div class="input-group">
                        <input type="text" name="time_casa" placeholder=" ">
                        <label>Time da casa</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="time_fora" placeholder=" ">
                        <label>Time visitante</label>
                    </div>
                    <div class="input-group">
                        <input type="number" name="placar_casa" placeholder=" " min="0">
                        <label>Placar casa</label>
                    </div>
                    <div class="input-group">
                        <input type="number" name="placar_fora" placeholder=" " min="0">
                        <label>Placar visitante</label>
                    </div>
                </div>

                <button class="btn" type="submit">Publicar</button>
            </form>
        </section>
    </main>

    <script>
    function previewImagem(event) {
        const arquivo = event.target.files[0];
        const img = document.getElementById("preview");

        if (!arquivo) {
            img.style.display = "none";
            return;
        }

        img.src = URL.createObjectURL(arquivo);
        img.style.display = "block";
    }
    </script>
</body>
</html>
