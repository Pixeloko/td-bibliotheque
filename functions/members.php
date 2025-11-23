<?php
declare(strict_types=1);

/**
 * Donne les données du membre via son id
 * @param $id Id du membre
 * @return ?array données du membre || []
 */
function getMemberById(int $id): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = :id");
    $stmt->execute(['id' => $id]);

    $member = $stmt->fetch();
    return $member ?: null;
}

/**
 * Donne les données du membre, via son email
 * @param $email email du membre
 * @return ?array array avec ses données || []
 */
function getMemberByEmail(string $email): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT * FROM members WHERE email = :email");
    $stmt->execute(['email' => $email]);

    $member = $stmt->fetch();
    return $member ?: null;
}

/**
 * Création d'un membre
 * @param $firstname prénom
 * @param $lastname nom de famille
 * @param $email email
 * @param $password mot de passe
 * @return int id du nouveau membre
 */
function createMember(string $firstname, string $lastname, string $email, string $password): int
{
    $pdo = getDatabase();

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email invalide.");
    }

    if (getMemberByEmail($email) !== null) {
        throw new Exception("Cet email existe déjà.");
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $date = date("Y-m-d");

    $stmt = $pdo->prepare("
        INSERT INTO members (firstname, lastname, email, password, membership_date)
        VALUES (:firstname, :lastname, :email, :password, :membership_date)
    ");

    $stmt->execute([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'password' => $password,
        'membership_date' => $date
    ]);

    return intval($pdo->lastInsertId());
}
