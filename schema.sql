CREATE DATABASE IF NOT EXISTS bibliotheque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bibliotheque;

-- Table des livres
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    available_copies INT DEFAULT 1,
    total_copies INT DEFAULT 1,
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- Table des membres
CREATE TABLE members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    membership_date DATE NOT NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB;

-- Table des emprunts
CREATE TABLE borrowings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    book_id INT NOT NULL,
    member_id INT NOT NULL,
    borrowed_at DATE NOT NULL,
    due_date DATE NOT NULL,
    returned_at DATE DEFAULT NULL,
    late_days INT DEFAULT 0,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    INDEX idx_member (member_id)
) ENGINE=InnoDB;

-- Données de test
INSERT INTO books (title, author, category, available_copies, total_copies) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupéry', 'Roman', 3, 3),
('1984', 'George Orwell', 'Science-Fiction', 2, 2),
("L\'Étranger", "Albert Camus", "Roman", 1, 2),
('Candide', 'Voltaire', 'Philosophie', 4, 4);

INSERT INTO members (firstname, lastname, email, password, membership_date) VALUES
('Jean', 'Dupont', 'jean.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-15'),
('Marie', 'Martin', 'marie.martin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-02-01');

-- Tests
use bibliotheque;
DESCRIBE borrowings;
SELECT borrowed_at, due_date FROM borrowings;
SELECT * FROM borrowings;
SELECT* FROM books;

DELETE FROM members WHERE firstname = 'marie';