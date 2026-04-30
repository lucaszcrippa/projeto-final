<?php
include 'config/conexao.php';

$busca = trim($_GET['busca'] ?? '');
$params = [];
$types = '';
$where = '';

if ($busca !== '') {
    $where = "WHERE n.titulo LIKE ?";
    $params[] = "%{$busca}%";
    $types .= 's';
}

$sql = "SELECT
            n.id AS noticia_id,
            n.titulo,
            n.noticia,
            n.imagem,
            n.data,
            u.nome AS autor_nome,
            j.time_casa,
            j.time_fora,
            j.placar_casa,
            j.placar_fora,
            COUNT(l.id) AS total_likes
        FROM noticias n
        LEFT JOIN jogos j ON j.noticia_id = n.id
        LEFT JOIN usuarios u ON u.id = n.autor
        LEFT JOIN likes l ON l.noticia_id = n.id
        $where
        GROUP BY n.id, j.id, u.nome
        ORDER BY n.data DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$noticias = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
$manchete = $noticias[0] ?? null;
$lista = array_slice($noticias, $manchete ? 1 : 0);
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Arena Esportiva | Notícias, placares e bastidores</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="index.php">
            <span class="brand-mark">AE</span>
            <span>Arena Esportiva</span>
        </a>

        <nav class="nav-actions" aria-label="Navegação principal">
            <a class="link-muted" href="index.php">Início</a>
            <a class="link-muted" href="#noticias">Notícias</a>
            <?php if (isset($_SESSION['user'])) { ?>
                <span class="user-pill">Olá, <?php echo e($_SESSION['user']['nome']); ?></span>
                <a class="link-muted" href="perfil.php">Meu perfil</a>
                <a class="btn btn-small" href="create_noticia.php">Nova notícia</a>
                <a class="link-muted" href="logout.php">Sair</a>
            <?php } else { ?>
                <a class="btn btn-small" href="login.php">Entrar</a>
            <?php } ?>
        </nav>
    </header>

    <main class="page-shell">
        <section class="ticker" aria-label="Resumo do portal">
            <strong>Em destaque</strong>
            <span><?php echo count($noticias); ?> notícias publicadas</span>
            <span>Cobertura de jogos, bastidores e análise</span>
            <span>Portal atualizado pela redação</span>
        </section>

        <section class="hero">
            <div>
                <p class="eyebrow">Notícias esportivas</p>
                <h1>O resumo do jogo, a análise e os bastidores em um só lugar.</h1>
                <p class="hero-text">Acompanhe placares, publique notícias e mantenha a torcida informada com um portal mais rápido de ler e mais bonito de navegar.</p>
            </div>

            <form class="search-box" method="GET">
                <label for="busca">Buscar notícia</label>
                <div class="search-row">
                    <input id="busca" type="search" name="busca" placeholder="Digite time, campeonato ou título" value="<?php echo e($busca); ?>">
                    <button class="btn" type="submit">Buscar</button>
                </div>
            </form>
        </section>

        <?php if ($manchete) { ?>
            <section class="featured-grid">
                <article class="featured-card">
                    <div class="featured-image">
                        <?php if (!empty($manchete['imagem'])) { ?>
                            <img src="img/<?php echo e($manchete['imagem']); ?>" alt="<?php echo e($manchete['titulo']); ?>">
                        <?php } else { ?>
                            <div class="image-placeholder">
                                <span>Arena Esportiva</span>
                                <strong>Esporte em pauta</strong>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="featured-content">
                        <span class="tag">Manchete</span>
                        <h2><?php echo e($manchete['titulo']); ?></h2>
                        <p><?php echo e(resumo($manchete['noticia'], 210)); ?></p>
                        <div class="meta-line">
                            <span><?php echo e($manchete['autor_nome'] ?: 'Redação'); ?></span>
                            <span><?php echo e(date('d/m/Y H:i', strtotime($manchete['data']))); ?></span>
                            <span><?php echo (int) $manchete['total_likes']; ?> curtidas</span>
                        </div>
                        <?php if (!empty($manchete['time_casa']) || !empty($manchete['time_fora'])) { ?>
                            <div class="score-card">
                                <span><?php echo e($manchete['time_casa']); ?></span>
                                <strong><?php echo e($manchete['placar_casa']); ?> x <?php echo e($manchete['placar_fora']); ?></strong>
                                <span><?php echo e($manchete['time_fora']); ?></span>
                            </div>
                        <?php } ?>
                        <div class="card-actions">
                            <a class="btn" href="view.php?id=<?php echo (int) $manchete['noticia_id']; ?>">Ler notícia</a>
                            <?php if (isset($_SESSION['user'])) { ?>
                                <a class="link-muted" href="edit_noticia.php?id=<?php echo (int) $manchete['noticia_id']; ?>">Editar</a>
                            <?php } ?>
                        </div>
                    </div>
                </article>
            </section>
        <?php } ?>

        <section class="section-heading" id="noticias">
            <div>
                <p class="eyebrow">Últimas atualizações</p>
                <h2><?php echo $busca ? 'Resultados da busca' : 'Mais notícias'; ?></h2>
            </div>
            <?php if ($busca) { ?>
                <a class="link-muted" href="index.php">Limpar busca</a>
            <?php } ?>
        </section>

        <section class="news-grid">
            <?php foreach ($lista as $d) { ?>
                <article class="news-card">
                    <?php if (!empty($d['imagem'])) { ?>
                        <img src="img/<?php echo e($d['imagem']); ?>" alt="<?php echo e($d['titulo']); ?>">
                    <?php } else { ?>
                        <div class="thumb-placeholder">
                            <span>AE</span>
                        </div>
                    <?php } ?>

                    <div class="news-body">
                        <h3><?php echo e($d['titulo']); ?></h3>
                        <p><?php echo e(resumo($d['noticia'])); ?></p>

                        <?php if (!empty($d['time_casa']) || !empty($d['time_fora'])) { ?>
                            <div class="score-mini">
                                <?php echo e($d['time_casa']); ?>
                                <strong><?php echo e($d['placar_casa']); ?> x <?php echo e($d['placar_fora']); ?></strong>
                                <?php echo e($d['time_fora']); ?>
                            </div>
                        <?php } ?>

                        <div class="meta-line">
                            <span><?php echo e($d['autor_nome'] ?: 'Redação'); ?></span>
                            <span><?php echo (int) $d['total_likes']; ?> curtidas</span>
                        </div>

                        <div class="card-actions">
                            <?php if (isset($_SESSION['user'])) { ?>
                                <a href="like.php?id=<?php echo (int) $d['noticia_id']; ?>">Curtir</a>
                            <?php } ?>
                            <a href="view.php?id=<?php echo (int) $d['noticia_id']; ?>">Ver</a>
                            <?php if (isset($_SESSION['user'])) { ?>
                                <a href="edit_noticia.php?id=<?php echo (int) $d['noticia_id']; ?>">Editar</a>
                                <a class="danger-link" href="delete_noticia.php?id=<?php echo (int) $d['noticia_id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">Excluir</a>
                            <?php } ?>
                        </div>
                    </div>
                </article>
            <?php } ?>
        </section>

        <?php if (!$noticias) { ?>
            <div class="empty-state">
                <h2>Nenhuma notícia encontrada</h2>
                <p>Publique a primeira notícia ou tente buscar por outro termo.</p>
            </div>
        <?php } ?>

        <footer class="site-footer">
            <strong>Arena Esportiva</strong>
            <span>Cobertura esportiva com notícias, placares, bastidores e análise para quem acompanha o jogo de perto.</span>
        </footer>
    </main>
</body>
</html>
