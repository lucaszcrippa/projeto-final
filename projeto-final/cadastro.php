<?php
include 'config/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '' || $email === '' || strlen($senha) < 6) {
        $erro = 'Preencha todos os campos e use uma senha com pelo menos 6 caracteres.';
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO usuarios(nome, email, senha) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $hash);

        if (mysqli_stmt_execute($stmt)) {
            $sucesso = 'Cadastro criado com sucesso. Você já pode entrar.';
        } else {
            $erro = 'Não foi possível cadastrar. Verifique se o e-mail já está em uso.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro | Arena Esportiva</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="form-page">
        <section class="form-box auth-box">
            <a href="index.php" class="btn-voltar">Voltar para o portal</a>
            <p class="eyebrow">Nova conta</p>
            <h1>Cadastro</h1>

            <?php if ($erro) { ?>
                <p class="alert"><?php echo e($erro); ?></p>
            <?php } ?>

            <?php if ($sucesso) { ?>
                <p class="notice"><?php echo e($sucesso); ?></p>
            <?php } ?>

            <form method="POST">
                <div class="input-group">
                    <input type="text" name="nome" placeholder=" " required>
                    <label>Nome</label>
                </div>

                <div class="input-group">
                    <input type="email" name="email" placeholder=" " required>
                    <label>E-mail</label>
                </div>

                <div class="input-group password-field">
                    <input type="password" name="senha" id="senha" placeholder=" " minlength="6" required>
                    <label>Senha</label>
                    <button type="button" onclick="toggleSenha()">Mostrar</button>
                </div>

                <button class="btn" type="submit">Criar conta</button>
            </form>

            <p class="auth-switch">Já tem uma conta? <a href="login.php">Entrar</a></p>
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
