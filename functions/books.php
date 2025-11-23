<?php
declare(strict_types=1);

/**
 * Retourne les données (ou une array vide) via l'id du livre
 *
 * @param string $id L'id du livre
 * @return ?array array avec les clés-valeurs pour ce livre || array vide si livre non trouvé
 */
function getBookById(int $bookId): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute(['id' => $bookId]);

    $book = $stmt->fetch();

    return $book ?: null;
}

/**
 * Récupère tous les livres d'une catégorie
 * @param $category catégorie du livre
 * @return array avec tous les livres de cette catégorie
 */
function getBooksByCategory(string $category): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT * FROM books WHERE category = :category");
    $stmt->execute(['category' => $category]);

    return $stmt->fetchAll() ?: [];
}

/**
 * Vérifie si un livre est disponible
 * @param $bookId L'id du livre
 * @return bool True si disponible || False si indisponible
 */
function isBookAvailable(int $bookId): bool
{
    $book = getBookById($bookId);

    if ($book && isset($book['available_copies'])) {
        return intval($book['available_copies']) > 0;
    } else {
        return false;
    }
}

/**
 * Décrémente le nombre de copies disponibles
 * @param $bookId L'id du livre
 * @return bool True si décrémentation accomplie || False si copie disponible déjà à zéro ou livre inexistant
 */
function decrementAvailableCopies(int $bookId): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        UPDATE books
        SET available_copies = available_copies - 1
        WHERE id = :id AND available_copies > 0
    ");

    $stmt->execute(['id' => $bookId]);

    // Compte les modifications par la dernière requête
    return $stmt->rowCount() > 0;
}

/**
 * Incrémente le nombre de copies disponibles
 * @param $bookId L'id du livre
 * @return bool True si incrémentation accomplie || False si livre inexistant
 */
function incrementAvailableCopies(int $bookId): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        UPDATE books
        SET available_copies = available_copies + 1
        WHERE id = :id
    ");

    $stmt->execute(['id' => $bookId]);

    return $stmt->rowCount() > 0;
}
