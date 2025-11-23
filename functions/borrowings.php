<?php
declare(strict_types=1);

/**
 * Créer un emprunt
 * @param $bookId L'id du livre
 * @param $memberId Id du membre
 * @param $borrowedAt date de l'emprunt
 * @param $dueDate date limite de retour du livre (14j après l'emprunt)
 * @return int Id de l'emprunt
 */
function createBorrowing(int $bookId, int $memberId, string $borrowedAt, string $dueDate): int
{
    $pdo = getDatabase();

    // Vérifier la disponibilité
    if (!isBookAvailable($bookId)) {
        throw new Exception("Ce livre n'est pas disponible.");
    }

    // Vérifier si déjà trois emprunts
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE member_id = :memberId AND returned_at IS NULL");
    $stmt->execute(['memberId' => $memberId]);
    $activeBorrowings = $stmt->fetchColumn();
    if ($activeBorrowings >= 3) {
        throw new Exception("Vous avez atteint la limite d'emprunt.");
    }

    // Nouvel emprunt
    $stmt = $pdo->prepare("INSERT INTO borrowings (book_id, member_id, borrowed_at, due_date) VALUES (:bookId, :memberId, :borrowedAt, :dueDate)");
    $stmt->execute([
        'bookId' => $bookId,
        'memberId' => $memberId,
        'borrowedAt' => $borrowedAt,
        'dueDate' => $dueDate
    ]);

    // Donne l'id de l'emprunt
    return (int) $pdo->lastInsertId();
}

/**
 * Récupère les emprunts actifs (non retournés) d'un membre.
 *
 * @param int $memberId
 * @return array
 */
function getActiveBorrowingsByMemberId(int $memberId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("
        SELECT *
        FROM borrowings
        WHERE member_id = :member_id
        AND returned_at IS NULL
    ");

    $stmt->execute(['member_id' => $memberId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/** 
 * Retourne les données de l'emprunt
 * @param $id L'id de l'emprunt
 * @return ?array $borrowing (données de l'emprunt) || [] (array vide)
 * */

function getBorrowingById(int $id): ?array
{
    $pdo = getDatabase();

    // Récupère l'emprunt
    $stmt = $pdo->prepare("SELECT * FROM borrowings WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$borrowing) {
        return null; 
    }

    // Calcul des jours
    $now = new DateTime();
    $dueDate = new DateTime($borrowing['due_date']); 
    $lateDays = 0;
    if ($now > $dueDate) {
        $diff = $now->diff($dueDate);
        $lateDays = $diff->days; 
    }

    // Mise à jour
    $updateStmt = $pdo->prepare("UPDATE borrowings SET late_days = :lateDays WHERE id = :id");
    $updateStmt->execute([
        'lateDays' => $lateDays,
        'id' => $id
    ]);

    $borrowing['late_days'] = $lateDays;

    return $borrowing;
}

/** 
 * Marquer un emprunt comme retourné 
 * @param $borrowingId L'id de l'emprunt
 * @return bool False rien de modifié || True si retourné 
 * */
function markBorrowingAsReturned(int $borrowingId): bool
{
    $pdo = getDatabase();

    try {
        // Marquer comme retourné
        $stmt = $pdo->prepare("UPDATE borrowings SET returned_at = CURDATE() WHERE id = :id");
        $stmt->execute(['id' => $borrowingId]);

        if ($stmt->rowCount() === 0) {
            // S'il n'y a eu aucune modification avec la dernière requête
            return false;
        }

        // Calculer les jours en retard
        $stmtDate = $pdo->prepare("SELECT DATEDIFF(CURDATE(), borrowed_at) AS late_days FROM borrowings WHERE id = :id");
        $stmtDate->execute(['id' => $borrowingId]);
        $result = $stmtDate->fetch(PDO::FETCH_ASSOC);
        $lateDays = max(0, $result['late_days'] ?? 0); // Compare deux valeurs, si c'est 0 retourne false

        // Mettre à jour les jours en retard
        $stmtUpdate = $pdo->prepare("UPDATE borrowings SET late_days = :late_days WHERE id = :id");
        $stmtUpdate->execute([
            'late_days' => $lateDays,
            'id' => $borrowingId
        ]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}

/** 
 * Logique métier complète pour emprunter
 * @param $bookId L'id du livre
 * @param $memberId Id du membre
 * @return int l'id de l'emprunt
 * */
function borrowBook(int $bookId, int $memberId): int
{
    try {
        $pdo = getDatabase();

        // Vérifier la disponibilité du livre
        $stmt = $pdo->prepare("SELECT available_copies FROM books WHERE id = :id");
        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$book || $book['available_copies'] <= 0) {
            throw new Exception("Livre non disponible ou inexistant.");
        }

        // Vérifier le nombre d'emprunts actifs du membre (limite à 3)
        $stmtM = $pdo->prepare("SELECT COUNT(*) AS active_borrowings FROM borrowings WHERE member_id = :memberId AND returned_at IS NULL");
        $stmtM->execute(['memberId' => $memberId]);
        $memberData = $stmtM->fetch(PDO::FETCH_ASSOC);
        if ($memberData['active_borrowings'] >= MAX_ACTIVE_BORROWINGS) {
            throw new Exception("Le membre a atteint la limite de 3 emprunts.");
        }

        // Calculer les dates
        $borrowedAt = date('Y-m-d'); // Date du jour
        $dueDate = date('Y-m-d', strtotime('+14 days')); // +14 jours

        // Créer l'emprunt
        $borrowingId = createBorrowing($bookId, $memberId, $borrowedAt, $dueDate);

        // Décrémenter les copies disponibles
        decrementAvailableCopies($bookId);

        return $borrowingId; // Retourner l'ID de l'emprunt
    } catch (Exception $e) {
        // Lever l'exception pour que l'appelant la gère
        throw $e;
    }
}


?>