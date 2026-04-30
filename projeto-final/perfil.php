<?php
include 'config/conexao.php';
require_login();

$usuarioId = (int) $_SESSION['user']['id'];
$erro = '';
$sucesso = '';

$stmtUsuario = mysqli_prepare($conn, "SELECT id, nome, email, data_criacao FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmtUsuario, 'i', $usuarioId);
mysqli_stmt_execute($stmtUsuario);
$usuario = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUsuario));

if (!$usuario) {
    session_destroy();
    redirect('login.php');
}

$inicial = strtoupper(substr($usuario['nome'], 0, 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $email === '') {
        $erro = 'Preencha nome e e-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } else {
        $stmtEmail = mysqli_prepare($conn, "SELECT id FROM usuarios WHERE email = ? AND id <> ?");
        mysqli_stmt_bind_param($stmtEmail, 'si', $email, $usuarioId);
        mysqli_stmt_execute($stmtEmail);
        $emailEmUso = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtEmail));

        if ($emailEmUso) {
            $erro = 'Este e-mail já está em uso por outra conta.';
        }
    }

    if (!$erro && ($senhaAtual !== '' || $novaSenha !== '' || $confirmarSenha !== '')) {
        $stmtSenha = mysqli_prepare($conn, "SELECT senha FROM usuarios WHERE id = ?");
        mysqli_stmt_bind_param($stmtSenha, 'i', $usuarioId);
        mysqli_stmt_execute($stmtSenha);
        $dadosSenha = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtSenha));

        if (!$dadosSenha || !password_verify($senhaAtual, $dadosSenha['senha'])) {
            $erro = 'A senha atual está incorreta.';
        } elseif (strlen($novaSenha) < 6) {
            $erro = 'A nova senha precisa ter pelo menos 6 caracteres.';
        } elseif ($novaSenha !== $confirmarSenha) {
            $erro = 'A confirmação da nova senha não confere.';
        }
    }

    if (!$erro) {
        if ($novaSenha !== '') {
            $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmtUpdate = mysqli_prepare($conn, "UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, 'sssi', $nome, $email, $hash, $usuarioId);
        } else {
            $stmtUpdate = mysqli_prepare($conn, "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, 'ssi', $nome, $email, $usuarioId);
        }

        mysqli_stmt_execute($stmtUpdate);

        $_SESSION['user']['nome'] = $nome;
        $_SESSION['user']['email'] = $email;
        $usuario['nome'] = $nome;
        $usuario['email'] = $email;
        $sucesso = 'Perfil atualizado com sucesso.';
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meu perfil | Arena Esportiva</title>
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
            <a class="link-muted" href="create_noticia.php">Nova notícia</a>
            <a class="link-muted" href="logout.php">Sair</a>
        </nav>
    </header>

    <main class="profile-page">
        <section class="profile-hero">
            <div class="profile-avatar"><?php echo e($inicial); ?></div>
            <div>
                <p class="eyebrow">Conta da redação</p>
                <h1>Meu perfil</h1>
                <p>Atualize seus dados de acesso e mantenha sua assinatura de autor correta nas notícias.</p>
            </div>
        </section>

        <section class="profile-grid">
            <aside class="profile-summary">
                <span class="tag">Usuário ativo</span>
                <h2><?php echo e($usuario['nome']); ?></h2>
                <p><?php echo e($usuario['email']); ?></p>
                <div class="profile-stat">
                    <strong><?php echo e(date('d/m/Y', strtotime($usuario['data_criacao']))); ?></strong>
                    <span>Cadastro criado</span>
                </div>
            </aside>

            <section class="form-box profile-form">
                <?php if ($erro) { ?>
                    <p class="alert"><?php echo e($erro); ?></p>
                <?php } ?>

                <?php if ($sucesso) { ?>
                    <p class="notice"><?php echo e($sucesso); ?></p>
                <?php } ?>

                <form method="POST">
                    <h2>Dados pessoais</h2>
                    <div class="grid">
                        <div class="input-group">
                            <input type="text" name="nome" placeholder=" " value="<?php echo e($usuario['nome']); ?>" required>
                            <label>Nome</label>
                        </div>

                        <div class="input-group">
                            <input type="email" name="email" placeholder=" " value="<?php echo e($usuario['email']); ?>" required>
                            <label>E-mail</label>
                        </div>
                    </div>

                    <div class="form-divider"></div>

                    <h2>Alterar senha</h2>
                    <p class="form-hint">Preencha estes campos somente se quiser trocar sua senha.</p>

                    <div class="input-group password-field">
                        <input type="password" name="senha_atual" id="senha_atual" placeholder=" ">
                        <label>Senha atual</label>
                        <button type="button" onclick="togglePassword('senha_atual', this)">Mostrar</button>
                    </div>

                    <div class="grid">
                        <div class="input-group password-field">
                            <input type="password" name="nova_senha" id="nova_senha" placeholder=" " minlength="6">
                            <label>Nova senha</label>
                            <button type="button" onclick="togglePassword('nova_senha', this)">Mostrar</button>
                        </div>

                        <div class="input-group password-field">
                            <input type="password" name="confirmar_senha" id="confirmar_senha" placeholder=" " minlength="6">
                            <label>Confirmar senha</label>
                            <button type="button" onclick="togglePassword('confirmar_senha', this)">Mostrar</button>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button class="btn" type="submit">Salvar perfil</button>
                        <a class="link-muted" href="index.php">Cancelar</a>
                    </div>
                </form>
            </section>
        </section>
    </main>

    <script>
    function togglePassword(id, button) {
        const input = document.getElementById(id);
        const visible = input.type === "text";
        input.type = visible ? "password" : "text";
        button.textContent = visible ? "Mostrar" : "Ocultar";
    }
    </script>
</body>
</html>
