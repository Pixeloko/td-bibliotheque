<?php
declare(strict_types=1);

function getDatabase(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=hostname;port=yourport;dbname=bibliotheque;charset=utf8",
                'yourroot',
                'yourpassword',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // En production, logger l'erreur au lieu de l'afficher
            error_log($e->getMessage());
            die('Erreur de connexion à la base de données');
        }
    }

    return $pdo;
}