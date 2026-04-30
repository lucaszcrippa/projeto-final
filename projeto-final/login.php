<?php
include 'config/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT * FROM usuarios WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user'] = $user;
        redirect('index.php');
    }

    $erro = 'E-mail ou senha inválidos.';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar | Arena Esportiva</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="form-page">
        <section class="form-box auth-box">
            <a href="index.php" class="btn-voltar">Voltar para o portal</a>
            <p class="eyebrow">Acesso editorial</p>
            <h1>Entrar</h1>

            <?php if ($erro) { ?>
                <p class="alert"><?php echo e($erro); ?></p>
            <?php } ?>

            <form method="POST">
                <div class="input-group">
                    <input type="email" name="email" placeholder=" " required>
                    <label>E-mail</label>
                </div>

                <div class="input-group password-field">
                    <input type="password" name="senha" id="senha" placeholder=" " required>
                    <label>Senha</label>
                    <button type="button" onclick="toggleSenha()">Mostrar</button>
                </div>

                <button class="btn" type="submit">Entrar</button>
            </form>

            <p class="auth-switch">Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
        </section>
    </main>

    <script>
    function toggleSenha() {
        const input = document.getElementById("senha");
        const button = event.currentTarget;
        const visible = input.type === "text";
        input.type = visible ? "password" : "text";
        button.textContent = visible ? "Mostrar" : "Ocultar";
    }
    </script>
</body>
</html>
