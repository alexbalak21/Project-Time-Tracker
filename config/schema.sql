CREATE DATABASE IF NOT EXISTS project_time_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE project_time_tracker;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    UNIQUE KEY users_email_unique (email)
);

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(255) NULL,
    description TEXT NULL,
    status ENUM('active', 'on_hold', 'completed') NOT NULL DEFAULT 'active',
    start_date DATE NULL,
    end_date DATE NULL,
    owner_user_id INT UNSIGNED NULL,
    client_name VARCHAR(255) NULL,
    color VARCHAR(50) NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT projects_owner_user_fk FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    user_id INT UNSIGNED NULL,
    project_id INT UNSIGNED NULL,
    is_generic TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT tasks_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT tasks_project_fk FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS time_entries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    project_id INT UNSIGNED NOT NULL,
    task_id INT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    duration_minutes INT UNSIGNED NOT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT time_entries_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT time_entries_project_fk FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT time_entries_task_fk FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);