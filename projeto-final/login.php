<?php include 'config/conexao.php'; ?>
<link rel="stylesheet" href="css/style.css">
<div class="container">
<h2>Login</h2>
<form method="POST">
<input type="email" name="email" placeholder="Digite seu email">
<div style="position: relative;">
    <input type="password" name="senha" id="senha" placeholder="Digite sua senha">
    
    <span onclick="toggleSenha()" 
    style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
        👁️
    </span>
</div>
<button>Entrar</button>
</form>

<p>Não tem uma conta? 
    <a href="cadastro.php">Cadastre-se</a>
</p>
<?php
if($_POST){
$sql = mysqli_query($conn,"SELECT * FROM usuarios WHERE email='{$_POST['email']}'");
$user = mysqli_fetch_assoc($sql);

if($user && password_verify($_POST['senha'],$user['senha'])){
$_SESSION['user']=$user;
header("Location:index.php");
}
}
?>
</div>
<script>
function toggleSenha() {
    var input = document.getElementById("senha");
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}
</script>