-- =============================================================================
-- AI Knowledge Hub — esquema MySQL 8+ (phpMyAdmin / mysqli)
-- Charset: utf8mb4 para suportar emojis e acentuação correta.
-- Importar: phpMyAdmin → Base de dados → Importar → escolher este ficheiro.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Opcional: criar base dedicada (comente se já estiver na BD certa)
-- CREATE DATABASE IF NOT EXISTS knowledgehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE knowledgehub;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Lookup: departamentos (alinhado a auth_department_options())
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS departments (
    code VARCHAR(32) NOT NULL,
    label VARCHAR(80) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO departments (code, label, sort_order) VALUES
    ('gerat', 'GERAT', 10),
    ('gelic', 'GELIC', 20),
    ('gecov', 'GECOV', 30),
    ('gecre', 'GECRE', 40),
    ('juridco', 'JURIDCO', 50),
    ('sutec', 'SUTEC', 60),
    ('diretoria', 'DIRETORIA', 70),
    ('rh', 'RH', 80),
    ('outro', 'Outro setor', 100),
    ('nao_identificado', 'Não identificado', 200)
ON DUPLICATE KEY UPDATE label = VALUES(label);

-- -----------------------------------------------------------------------------
-- Utilizadores (substituir / complementar login só em sessão)
-- password_hash: NULL enquanto não houver senha; usar password_hash() no PHP.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(160) NOT NULL,
    email VARCHAR(255) NULL,
    department_code VARCHAR(32) NOT NULL DEFAULT 'outro',
    role ENUM('colaborador','gestor','administrador') NOT NULL DEFAULT 'colaborador',
    password_hash VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_department (department_code),
    KEY idx_users_role (role),
    CONSTRAINT fk_users_department FOREIGN KEY (department_code) REFERENCES departments (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Publicações do hub (posts)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tool_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    summary VARCHAR(255) NOT NULL DEFAULT '',
    video_url VARCHAR(2048) NOT NULL DEFAULT '',
    tool_url VARCHAR(2048) NOT NULL DEFAULT '',
    article_url VARCHAR(2048) NOT NULL DEFAULT '',
    category ENUM('automacao','design','texto','dados') NOT NULL DEFAULT 'automacao',
    status ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
    learning_tips TEXT NULL,
    prompts_used TEXT NULL,
    author VARCHAR(160) NOT NULL DEFAULT '',
    department_code VARCHAR(32) NOT NULL DEFAULT 'outro',
    user_id BIGINT UNSIGNED NULL,
    preview_title VARCHAR(500) NOT NULL DEFAULT '',
    thumbnail VARCHAR(2048) NOT NULL DEFAULT '',
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_posts_category (category),
    KEY idx_posts_status (status),
    KEY idx_posts_department (department_code),
    KEY idx_posts_published_at (published_at),
    KEY idx_posts_user (user_id),
    FULLTEXT KEY ft_posts_search (tool_name, summary, description, preview_title),
    CONSTRAINT fk_posts_department FOREIGN KEY (department_code) REFERENCES departments (code),
    CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Documento anexo (até 1 por publicação, como na app actual)
CREATE TABLE IF NOT EXISTS post_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(512) NOT NULL DEFAULT '',
    public_url VARCHAR(2048) NOT NULL DEFAULT '',
    original_name VARCHAR(255) NOT NULL DEFAULT 'Documento anexo',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_post_documents_post (post_id),
    CONSTRAINT fk_post_documents_post FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Galeria de fotos por publicação
CREATE TABLE IF NOT EXISTS post_photos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(512) NOT NULL DEFAULT '',
    public_url VARCHAR(2048) NOT NULL DEFAULT '',
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_post_photos_post (post_id, sort_order),
    CONSTRAINT fk_post_photos_post FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Inovação: períodos (mensal / trimestral) + setor + documentos de evidência
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS innovation_periods (
    code VARCHAR(32) NOT NULL,
    label VARCHAR(80) NOT NULL,
    PRIMARY KEY (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO innovation_periods (code, label) VALUES
    ('mensal', 'Mensal'),
    ('trimestral', 'Trimestral')
ON DUPLICATE KEY UPDATE label = VALUES(label);

CREATE TABLE IF NOT EXISTS innovation_sectors (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    period_code VARCHAR(32) NOT NULL,
    sector_slug VARCHAR(64) NOT NULL,
    name VARCHAR(128) NOT NULL,
    custom_summary TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_innovation_sector_period_slug (period_code, sector_slug),
    KEY idx_innovation_sectors_period (period_code),
    CONSTRAINT fk_innovation_sectors_period FOREIGN KEY (period_code) REFERENCES innovation_periods (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS innovation_documents (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    innovation_sector_id BIGINT UNSIGNED NOT NULL,
    label VARCHAR(255) NOT NULL,
    content TEXT NULL,
    evidence_type ENUM('documento','caso_uso','treinamento','resultado','padronizacao') NOT NULL DEFAULT 'documento',
    author_name VARCHAR(160) NOT NULL DEFAULT '',
    author_department VARCHAR(32) NOT NULL DEFAULT 'outro',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_innovation_docs_sector (innovation_sector_id, sort_order),
    CONSTRAINT fk_innovation_docs_sector FOREIGN KEY (innovation_sector_id) REFERENCES innovation_sectors (id) ON DELETE CASCADE,
    CONSTRAINT fk_innovation_docs_department FOREIGN KEY (author_department) REFERENCES departments (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Automação: pedidos, comentários e timeline
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS automation_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    requester VARCHAR(160) NOT NULL,
    requester_department VARCHAR(32) NOT NULL DEFAULT 'outro',
    sector VARCHAR(32) NOT NULL DEFAULT 'outro',
    type VARCHAR(64) NOT NULL DEFAULT 'automacao_processo',
    priority ENUM('baixa','media','alta','critica') NOT NULL DEFAULT 'media',
    frequency VARCHAR(32) NOT NULL DEFAULT 'sob_demanda',
    activity TEXT NOT NULL,
    need TEXT NOT NULL,
    expected_result TEXT NOT NULL,
    deadline DATE NULL,
    status VARCHAR(64) NOT NULL DEFAULT 'novo',
    assignee VARCHAR(160) NOT NULL DEFAULT 'Não atribuído',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_automation_status (status),
    KEY idx_automation_sector (sector),
    KEY idx_automation_priority (priority),
    KEY idx_automation_deadline (deadline),
    CONSTRAINT fk_automation_req_dep FOREIGN KEY (requester_department) REFERENCES departments (code),
    CONSTRAINT fk_automation_sec FOREIGN KEY (sector) REFERENCES departments (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS automation_comments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_id BIGINT UNSIGNED NOT NULL,
    author VARCHAR(160) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_automation_comments_request (request_id, created_at),
    CONSTRAINT fk_automation_comments_request FOREIGN KEY (request_id) REFERENCES automation_requests (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS automation_timeline (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_id BIGINT UNSIGNED NOT NULL,
    event_type VARCHAR(64) NOT NULL DEFAULT 'status',
    label VARCHAR(500) NOT NULL,
    author VARCHAR(160) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_automation_timeline_request (request_id, created_at),
    CONSTRAINT fk_automation_timeline_request FOREIGN KEY (request_id) REFERENCES automation_requests (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Analytics (eventos agregados — meta em JSON)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS analytics_events (
    id CHAR(24) NOT NULL,
    type VARCHAR(64) NOT NULL,
    module VARCHAR(64) NOT NULL,
    target_type VARCHAR(64) NULL,
    target_id VARCHAR(255) NULL,
    meta JSON NULL,
    duration_seconds INT UNSIGNED NULL,
    user_name VARCHAR(160) NOT NULL DEFAULT '',
    department_code VARCHAR(32) NOT NULL DEFAULT 'outro',
    department_label VARCHAR(160) NOT NULL DEFAULT '',
    occurred_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_analytics_module (module, occurred_at),
    KEY idx_analytics_type (type, occurred_at),
    KEY idx_analytics_occurred (occurred_at),
    CONSTRAINT fk_analytics_department FOREIGN KEY (department_code) REFERENCES departments (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fim do esquema. Próximo passo na app PHP: Repository passar a PDO/MySQL
-- em vez de $_SESSION, mantendo os mesmos nomes de campos quando possível.
