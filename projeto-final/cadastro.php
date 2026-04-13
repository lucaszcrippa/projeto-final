<?php include 'config/conexao.php'; ?>
<link rel="stylesheet" href="css/style.css">
<div class="container">
<h2>Cadastro</h2>
<form method="POST">
<input type="text" name="nome" placeholder="Digite seu nome">
<input type="email" name="email" placeholder="Digite seu email">
<div style="position: relative;">
    <input type="password" name="senha" id="senha" placeholder="Digite sua senha">
    
    <span onclick="toggleSenha()" 
    style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
        👁️
    </span>
</div>
<button>Cadastrar</button>
</form>
<p>Já tem uma conta? 
    <a href="login.php">Entrar</a>
</p>
<?php
if($_POST){
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
mysqli_query($conn,"INSERT INTO usuarios(nome,email,senha)
VALUES('{$_POST['nome']}','{$_POST['email']}','$senha')");
echo "OK";
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