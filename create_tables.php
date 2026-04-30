<?php
require 'includes/db.php';
$pdo->exec('CREATE TABLE IF NOT EXISTS post_likes (id INT AUTO_INCREMENT PRIMARY KEY, post_id INT NOT NULL, user_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unique_like (post_id, user_id));');
$pdo->exec('CREATE TABLE IF NOT EXISTS post_comments (id INT AUTO_INCREMENT PRIMARY KEY, post_id INT NOT NULL, user_id INT NOT NULL, content TEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP);');
$pdo->exec('CREATE TABLE IF NOT EXISTS post_saves (id INT AUTO_INCREMENT PRIMARY KEY, post_id INT NOT NULL, user_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unique_save (post_id, user_id));');
echo "Tables created successfully.\n";
