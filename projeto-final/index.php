<?php 
session_start();
include 'config/conexao.php';
?>

<link rel="stylesheet" href="css/style.css">

<div class="container">

<h1>⚽ Portal Esportivo</h1>

<?php if(isset($_SESSION['user'])){ ?>
    <p>Bem-vindo <?php echo $_SESSION['user']['nome']; ?></p>
    <a href="create_noticia.php">Nova Notícia</a> |
    <a href="logout.php">Sair</a>
<?php } else { ?>
    <a href="login.php">Login</a>
<?php } ?>

<form method="GET">
    <input type="text" name="busca" placeholder="Buscar..." 
    value="<?php echo isset($_GET['busca']) ? $_GET['busca'] : ''; ?>">
</form>

<?php
$where = "";

if(isset($_GET['busca']) && !empty($_GET['busca'])){
    $b = mysqli_real_escape_string($conn, $_GET['busca']);
    $where = "WHERE n.titulo LIKE '%$b%'";
}

$sql = mysqli_query($conn,"SELECT n.*, j.*, u.nome 
FROM noticias n
LEFT JOIN jogos j ON j.noticia_id = n.id
LEFT JOIN usuarios u ON u.id = n.autor
$where 
ORDER BY n.data DESC");

while($d = mysqli_fetch_assoc($sql)){
?>

<div class="card">
    <h2><?php echo $d['titulo']; ?></h2>

    <img src="img/<?php echo $d['imagem']; ?>" width="100%">

    <p><?php echo substr($d['noticia'],0,100); ?>...</p>

    <p><b>Autor:</b> <?php echo $d['nome']; ?></p>

    <div class="resultado">
        <?php echo $d['time_casa']; ?> 
        <?php echo $d['placar_casa']; ?> x 
        <?php echo $d['placar_fora']; ?> 
        <?php echo $d['time_fora']; ?>
    </div>

    <a href="like.php?id=<?php echo $d['id']; ?>">❤️ Curtir</a>
    <a href="view.php?id=<?php echo $d['id']; ?>">Ver</a>

    <?php if(isset($_SESSION['user'])){ ?>
        | <a href="edit_noticia.php?id=<?php echo $d['id']; ?>">Editar</a>
        | <a href="delete_noticia.php?id=<?php echo $d['id']; ?>" onclick="return confirm('Tem certeza?')">Excluir</a>
    <?php } ?>

</div>

<?php } ?>

</div>