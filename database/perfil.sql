USE sgpessoa;

CREATE TABLE IF NOT EXISTS perfil (
    codigoPerfil INT AUTO_INCREMENT PRIMARY KEY,
    nomePerfil   VARCHAR(30)  NOT NULL UNIQUE,
    descricao    VARCHAR(150) NOT NULL DEFAULT '',
    ver          TINYINT(1)   NOT NULL DEFAULT 0,
    criar        TINYINT(1)   NOT NULL DEFAULT 0,
    editar       TINYINT(1)   NOT NULL DEFAULT 0,
    remover      TINYINT(1)   NOT NULL DEFAULT 0,
    utilizadores TINYINT(1)   NOT NULL DEFAULT 0,
    logs         TINYINT(1)   NOT NULL DEFAULT 0,
    perfis       TINYINT(1)   NOT NULL DEFAULT 0
);

INSERT IGNORE INTO perfil (codigoPerfil, nomePerfil, descricao, ver, criar, editar, remover, utilizadores, logs, perfis) VALUES
(1, 'OPERADOR',      'Lista e cria registos',                1,1,0,0,0,0,0),
(2, 'SUPEROPERADOR', 'CRUD completo',                        1,1,1,1,0,0,0),
(3, 'ADMINISTRADOR', 'CRUD, utilizadores e perfis',          1,1,1,1,1,0,1),
(4, 'AUDITOR',       'CRUD e consulta de logs',              1,1,1,1,0,1,0);
