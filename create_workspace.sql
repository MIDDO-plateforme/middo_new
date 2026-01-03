CREATE TABLE workspace (
    id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT DEFAULT NULL,
    settings JSON NOT NULL,
    ai_preferences JSON NOT NULL,
    stats JSON NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    is_active TINYINT(1) NOT NULL,
    visibility VARCHAR(50) NOT NULL,
    INDEX IDX_8D940019A76ED395 (user_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_8D940019A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
) ENGINE = InnoDB;

CREATE TABLE workspace_collaborators (
    workspace_id INT NOT NULL,
    user_id INT NOT NULL,
    INDEX IDX_F6C5DDAD82D40A1F (workspace_id),
    INDEX IDX_F6C5DDADA76ED395 (user_id),
    PRIMARY KEY(workspace_id, user_id),
    CONSTRAINT FK_F6C5DDAD82D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id) ON DELETE CASCADE,
    CONSTRAINT FK_F6C5DDADA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE workspace_document (
    id INT AUTO_INCREMENT NOT NULL,
    workspace_id INT NOT NULL,
    author_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT DEFAULT NULL,
    type VARCHAR(50) NOT NULL,
    category VARCHAR(50) DEFAULT NULL,
    file_path VARCHAR(500) DEFAULT NULL,
    mime_type VARCHAR(100) DEFAULT NULL,
    file_size BIGINT DEFAULT NULL,
    status VARCHAR(50) NOT NULL,
    ai_metadata JSON NOT NULL,
    history JSON NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    sent_at DATETIME DEFAULT NULL,
    INDEX IDX_4F636E1582D40A1F (workspace_id),
    INDEX IDX_4F636E15F675F31B (author_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_4F636E1582D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id),
    CONSTRAINT FK_4F636E15F675F31B FOREIGN KEY (author_id) REFERENCES user (id)
) ENGINE = InnoDB;

CREATE TABLE workspace_project (
    id INT AUTO_INCREMENT NOT NULL,
    workspace_id INT NOT NULL,
    owner_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT DEFAULT NULL,
    status VARCHAR(50) NOT NULL,
    priority VARCHAR(20) NOT NULL,
    progress INT NOT NULL,
    budget NUMERIC(10, 2) DEFAULT NULL,
    spent NUMERIC(10, 2) DEFAULT NULL,
    dashboard_config JSON NOT NULL,
    ai_metrics JSON NOT NULL,
    start_date DATETIME DEFAULT NULL,
    deadline DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX IDX_F78E3BE582D40A1F (workspace_id),
    INDEX IDX_F78E3BE57E3C61F9 (owner_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_F78E3BE582D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id),
    CONSTRAINT FK_F78E3BE57E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)
) ENGINE = InnoDB;

CREATE TABLE project_team_members (
    workspace_project_id INT NOT NULL,
    user_id INT NOT NULL,
    INDEX IDX_907E47ABFFDA9693 (workspace_project_id),
    INDEX IDX_907E47ABA76ED395 (user_id),
    PRIMARY KEY(workspace_project_id, user_id),
    CONSTRAINT FK_907E47ABFFDA9693 FOREIGN KEY (workspace_project_id) REFERENCES workspace_project (id) ON DELETE CASCADE,
    CONSTRAINT FK_907E47ABA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
) ENGINE = InnoDB;

CREATE TABLE workspace_task (
    id INT AUTO_INCREMENT NOT NULL,
    workspace_id INT NOT NULL,
    project_id INT DEFAULT NULL,
    creator_id INT NOT NULL,
    assigned_to_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT DEFAULT NULL,
    status VARCHAR(50) NOT NULL,
    priority VARCHAR(20) NOT NULL,
    estimated_hours DOUBLE PRECISION DEFAULT NULL,
    actual_hours DOUBLE PRECISION DEFAULT NULL,
    tags JSON NOT NULL,
    ai_suggestions JSON NOT NULL,
    checklist JSON NOT NULL,
    due_date DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX IDX_169CA8E782D40A1F (workspace_id),
    INDEX IDX_169CA8E7166D1F9C (project_id),
    INDEX IDX_169CA8E761220EA6 (creator_id),
    INDEX IDX_169CA8E7F4BD7827 (assigned_to_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_169CA8E782D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id),
    CONSTRAINT FK_169CA8E7166D1F9C FOREIGN KEY (project_id) REFERENCES workspace_project (id) ON DELETE SET NULL,
    CONSTRAINT FK_169CA8E761220EA6 FOREIGN KEY (creator_id) REFERENCES user (id),
    CONSTRAINT FK_169CA8E7F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)
) ENGINE = InnoDB;
