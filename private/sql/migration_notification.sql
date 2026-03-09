CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data TEXT DEFAULT NULL,
    action_url VARCHAR(255) DEFAULT NULL,
    action_label VARCHAR(100) DEFAULT NULL,
    is_read BOOLEAN NOT NULL DEFAULT 0,
    read_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL,
    icon VARCHAR(50) DEFAULT NULL,
    priority VARCHAR(20) DEFAULT 'normal',
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_user_read ON notifications(user_id, is_read);
CREATE INDEX IF NOT EXISTS idx_created ON notifications(created_at);
CREATE INDEX IF NOT EXISTS idx_type ON notifications(type);
