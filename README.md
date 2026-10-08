# LabActivity7-Braulio-PDO

A text-only PHP/PDO blog feed for the G-Site lab activity.

## Setup

1. Start Apache and MySQL in XAMPP.
2. In phpMyAdmin, select or create the `blog_site` database and run the SQL shown below. It creates exactly three tables: `users`, `posts`, and `comments`.
3. Confirm the MySQL credentials in `db.php` (the default XAMPP `root` account has no password).
4. Visit `http://localhost/LabActivity7-Braulio-PDO/register.php`.

The application uses PDO prepared statements, native `password_hash`/`password_verify`, sessions, CSRF tokens, server-side validation, browser constraint validation, ownership checks, and escaped output.

## phpMyAdmin SQL

```sql
CREATE DATABASE IF NOT EXISTS blog_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_site;

CREATE TABLE users (
	user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE posts (
	post_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	user_id INT UNSIGNED NOT NULL,
	body TEXT NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP NULL DEFAULT NULL,
	CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE comments (
	comment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	post_id INT UNSIGNED NOT NULL,
	user_id INT UNSIGNED NOT NULL,
	body TEXT NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP NULL DEFAULT NULL,
	CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
	CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;
```
