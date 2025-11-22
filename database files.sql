-- db.sql - Schema and seed for MyBookstore (MySQL/MariaDB)
-- Run this via phpMyAdmin (Import) or MySQL client.

CREATE DATABASE IF NOT EXISTS `bookstore` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bookstore`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','buyer') NOT NULL DEFAULT 'buyer',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB;

-- Books table
CREATE TABLE IF NOT EXISTS `books` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `category` VARCHAR(100) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `description` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Purchases table
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `book_id` INT NOT NULL,
  `purchase_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_book` (`user_id`, `book_id`),
  CONSTRAINT `fk_purchases_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_purchases_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed initial books
INSERT INTO `books` (`title`, `author`, `price`, `category`, `image`, `featured`, `description`) VALUES
('Clean Code', 'Robert C. Martin', 29.99, 'Programming', 'assets/images/clean_code.jpg', 1, 'A handbook of agile software craftsmanship focusing on writing clean, maintainable code.'),
('The Pragmatic Programmer', 'Andrew Hunt, David Thomas', 27.50, 'Programming', 'assets/images/pragmatic_programmer.jpg', 1, 'Essential tips, philosophies, and practices for pragmatic software development.'),
('Atomic Habits', 'James Clear', 19.95, 'Self-Help', 'assets/images/atomic_habits.jpg', 0, 'An easy & proven way to build good habits and break bad ones.'),
('1984', 'George Orwell', 14.00, 'Fiction', 'assets/images/1984.jpg', 0, 'Dystopian social science fiction novel and cautionary tale about totalitarianism.');
