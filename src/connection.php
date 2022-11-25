<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=netflix;charset=utf8", "root", "");
} catch (Exception $e) {
    header("location: index.php?error=1&message='Connexion échouée : Impossible de communiquer avec la base de donnée'");
}
?>