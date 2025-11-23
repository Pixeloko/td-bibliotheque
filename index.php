<?php
require_once "./functions/books.php";
require_once "./functions/borrowings.php";
require_once "./functions/members.php";
require_once "./config/database.php";

?>

<!--Inscription d'un nouveau membre-->
<div><?php 
try {
    echo "✅ Le membre avec l'ID =" . 
    createMember("Jean", "Bon", "jean-bon@email.com", "passw")
    . " a bien été créé.";
} catch(Exception $e) {
    echo $e->getMessage();
    // Retourne l'erreur
}
//echo $messageCreate;
?></div>

<!--Recherche de livre par catégorie-->
<?php $allBooks = getBooksByCategory("Roman");
foreach ($allBooks as $book):?>

<h2>📚 Infos sur les livres de la catégorie <?php $book['category']?></h2>

<div>
    <p>Titre : <?php echo htmlspecialchars($book['title']);?></p>
    <p>Auteur : <?php echo htmlspecialchars($book['author']);?></p>
    <p>Copies disponibles : <?php echo htmlspecialchars($book['available_copies']);?></p>
</div>

<?php endforeach;?>

<!--Emprunter un livre-->
<?php
try {
    $borrowingId = createBorrowing(1, 12, "2025-11-12", "2025-11-25");
    echo "✅ L'emprunt avec l'ID : $borrowingId a bien été créé.";
} catch(Exception $e) {
    echo $e->getMessage();
}
?>

<h2>📚 Les emprunts actifs</h2>

<?php 
$emprunts = getActiveBorrowingsByMemberId(12);

if (empty($emprunts)) {
    echo "<p>❌ Aucun emprunt actif.</p>";
}

foreach ($emprunts as $emprunt):
    $infoBook = getBookById($emprunt['book_id']);
?>

<div>
    <p><strong>Titre :</strong> <?= htmlspecialchars($infoBook['title']); ?></p>
    <p><strong>Auteur :</strong> <?= htmlspecialchars($infoBook['author']); ?></p>
    <p><strong>Catégorie :</strong> <?= htmlspecialchars($infoBook['category']); ?></p>
    <p><strong>À rendre pour :</strong> <?= htmlspecialchars($emprunt['due_date']); ?></p>
</div>
<hr>

<?php endforeach; ?>

<!-- Retour de livre -->
<h2>📚 Retour de livre</h2>
<div>
    <?php
try {
    $id = 4;
    markBorrowingAsReturned($id);
    echo "✅ L'emprunt " . $id . " a bien été retourné \n";
} catch(Exception $e) {
    echo $e->getMessage();
}
?>
<br>
<?php
try {
    $bookId = 1;
    incrementAvailableCopies($bookId);
    echo "✅ Le nouveau stock pour le livre (ID : " . $bookId . ") a bien été incrémenté \n";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</div>