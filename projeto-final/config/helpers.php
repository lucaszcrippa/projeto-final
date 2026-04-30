<?php
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header("Location: $path");
    exit;
}

function require_login() {
    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }
}

function resumo($texto, $limite = 150) {
    $texto = trim(strip_tags((string) $texto));

    if (function_exists('mb_strlen') && mb_strlen($texto, 'UTF-8') <= $limite) {
        return $texto;
    }

    if (function_exists('mb_substr')) {
        return mb_substr($texto, 0, $limite, 'UTF-8') . '...';
    }

    if (strlen($texto) <= $limite) {
        return $texto;
    }

    return substr($texto, 0, $limite) . '...';
}

function salvar_imagem_upload($campo, $imagemAtual = '') {
    if (empty($_FILES[$campo]['name']) || empty($_FILES[$campo]['tmp_name'])) {
        return $imagemAtual;
    }

    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($_FILES[$campo]['tmp_name']);

    if (!isset($permitidos[$mime])) {
        return $imagemAtual;
    }

    $nomeSeguro = uniqid('noticia_', true) . '.' . $permitidos[$mime];
    $destino = __DIR__ . '/../img/' . $nomeSeguro;

    if (move_uploaded_file($_FILES[$campo]['tmp_name'], $destino)) {
        return $nomeSeguro;
    }

    return $imagemAtual;
}
?>
