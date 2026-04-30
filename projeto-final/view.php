<?php
include 'config/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();

    $comentario = trim($_POST['comentario'] ?? '');

    if ($comentario !== '') {
        $stmtComentario = mysqli_prepare($conn, "INSERT INTO comentarios(texto, usuario_id, noticia_id, data) VALUES (?, ?, ?, NOW())");
        mysqli_stmt_bind_param($stmtComentario, 'sii', $comentario, $_SESSION['user']['id'], $id);
        mysqli_stmt_execute($stmtComentario);
    }

    redirect("view.php?id=$id");
}

$stmt = mysqli_prepare($conn, "SELECT n.*, u.nome AS autor_nome FROM noticias n LEFT JOIN usuarios u ON u.id = n.autor WHERE n.id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$noticia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$noticia) {
    redirect('index.php');
}

$stmtComentarios = mysqli_prepare($conn, "SELECT c.*, u.nome FROM comentarios c JOIN usuarios u ON u.id = c.usuario_id WHERE c.noticia_id = ? ORDER BY c.data DESC");
mysqli_stmt_bind_param($stmtComentarios, 'i', $id);
mysqli_stmt_execute($stmtComentarios);
$comentarios = mysqli_fetch_all(mysqli_stmt_get_result($stmtComentarios), MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($noticia['titulo']); ?> | Arena Esportiva</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="index.php">
            <span class="brand-mark">AE</span>
            <span>Arena Esportiva</span>
        </a>
        <nav class="nav-actions">
            <a class="link-muted" href="index.php">Voltar</a>
        </nav>
    </header>

    <main class="article-shell">
        <article class="article-card">
            <p class="eyebrow">Matéria</p>
            <h1><?php echo e($noticia['titulo']); ?></h1>
            <div class="meta-line">
                <span><?php echo e($noticia['autor_nome'] ?: 'Redação'); ?></span>
                <span><?php echo e(date('d/m/Y H:i', strtotime($noticia['data']))); ?></span>
            </div>

            <?php if (!empty($noticia['imagem'])) { ?>
                <img class="article-image" src="img/<?php echo e($noticia['imagem']); ?>" alt="<?php echo e($noticia['titulo']); ?>">
            <?php } ?>

            <div class="article-content">
                <?php echo nl2br(e($noticia['noticia'])); ?>
            </div>
        </article>

        <section class="comments-panel">
            <h2>Comentários</h2>

            <?php if (isset($_SESSION['user'])) { ?>
                <form method="POST" class="comment-form">
                    <label for="comentario">Escreva seu comentário</label>
                    <textarea id="comentario" name="comentario" rows="4" required></textarea>
                    <button class="btn" type="submit">Comentar</button>
                </form>
            <?php } else { ?>
                <p class="notice">Entre na sua conta para comentar.</p>
                <a class="btn btn-small" href="login.php">Entrar</a>
            <?php } ?>

            <div class="comment-list">
                <?php foreach ($comentarios as $c) { ?>
                    <div class="comment-item">
                        <strong><?php echo e($c['nome']); ?></strong>
                        <p><?php echo nl2br(e($c['texto'])); ?></p>
                    </div>
                <?php } ?>

                <?php if (!$comentarios) { ?>
                    <p class="notice">Ainda não há comentários nesta notícia.</p>
                <?php } ?>
            </div>
        </section>
    </main>
</body>
</html>
