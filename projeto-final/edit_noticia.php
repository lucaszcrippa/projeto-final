<?php
include 'config/conexao.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirect('index.php');
}

$stmt = mysqli_prepare($conn, "SELECT * FROM noticias WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$noticia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$noticia) {
    redirect('index.php');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $texto = trim($_POST['noticia'] ?? '');
    $novaImagem = salvar_imagem_upload('imagem', $noticia['imagem']);

    if ($titulo === '' || $texto === '') {
        $erro = 'Preencha o título e o conteúdo da notícia.';
    } else {
        if ($novaImagem !== $noticia['imagem'] && $noticia['imagem'] && file_exists("img/" . $noticia['imagem'])) {
            unlink("img/" . $noticia['imagem']);
        }

        $stmtUpdate = mysqli_prepare($conn, "UPDATE noticias SET titulo = ?, noticia = ?, imagem = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmtUpdate, 'sssi', $titulo, $texto, $novaImagem, $id);
        mysqli_stmt_execute($stmtUpdate);

        redirect('index.php');
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar notícia | Arena Esportiva</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="form-page">
        <section class="form-box">
            <a href="index.php" class="btn-voltar">Voltar</a>
            <p class="eyebrow">Edição</p>
            <h1>Editar notícia</h1>

            <?php if ($erro) { ?>
                <p class="alert"><?php echo e($erro); ?></p>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <input name="titulo" placeholder=" " value="<?php echo e($noticia['titulo']); ?>" required>
                    <label>Título da notícia</label>
                </div>

                <div class="input-group">
                    <textarea name="noticia" placeholder=" " rows="8" required><?php echo e($noticia['noticia']); ?></textarea>
                    <label>Conteúdo da notícia</label>
                </div>

                <?php if (!empty($noticia['imagem'])) { ?>
                    <div class="current-image">
                        <span>Imagem atual</span>
                        <img src="img/<?php echo e($noticia['imagem']); ?>" alt="<?php echo e($noticia['titulo']); ?>">
                    </div>
                <?php } ?>

                <div class="upload-box">
                    <label for="imagem">Trocar imagem</label>
                    <input type="file" name="imagem" id="imagem" accept="image/png,image/jpeg,image/webp">
                </div>

                <button class="btn" type="submit">Salvar alterações</button>
            </form>
        </section>
    </main>
</body>
</html>
