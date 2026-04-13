-- =========================
-- TABELA USUÁRIOS
-- =========================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABELA NOTÍCIAS
-- =========================
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    conteudo TEXT NOT NULL,
    imagem VARCHAR(255),
    id_usuario INT,
    data_postagem TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- =========================
-- TABELA COMENTÁRIOS
-- =========================
CREATE TABLE comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_noticia INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    comentario TEXT NOT NULL,
    data_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_noticia) REFERENCES noticias(id) ON DELETE CASCADE
);

-- =========================
-- TABELA LIKES ❤️
-- =========================
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_noticia INT NOT NULL,
    ip VARCHAR(50),

    FOREIGN KEY (id_noticia) REFERENCES noticias(id) ON DELETE CASCADE
);

-- =========================
-- USUÁRIO PADRÃO
-- senha: 123456
-- =========================
INSERT INTO usuarios (nome, email, senha)
VALUES (
    'Admin',
    'admin@site.com',
    '$2y$10$wHh9u1Wv3Q8QxJz9F6qf3uZ8uY7r3QeY9sX6yH3F5l2P8oK7X1a2W'
);

-- =========================
-- NOTÍCIA EXEMPLO (pra testar)
-- =========================
INSERT INTO noticias (titulo, conteudo, imagem)
VALUES (
    'Bem-vindo ao Portal Esportivo',
    'Seu site de fofocas esportivas está no ar 🔥',
    ''
);