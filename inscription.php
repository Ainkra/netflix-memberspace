<?php

//#########################################
//          SE CONNECTER À LA BDD
//#########################################
try {
    $db = new PDO("mysql:host=localhost;dbname=netflix;charset=utf8", "root", "");
} catch (Exception $e) {
    die("Erreur: ".$e->getMessage());
}
//#########################################
//            MANAGE INSCRIPTION
//#########################################
// Créer la session
session_start();

// Vérifier que les champs ne sont pas vide
if (!empty($_POST["email"]) && !empty($_POST['password']) && !empty($_POST['password_two'])) {

    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    $passwordTwo = htmlspecialchars($_POST['password_two']);
// Vérifier que l'adresse mail est valide
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location: inscription.php?error=1&message=Email invalide.");
    }

// Vérifier que les mots de passe 1 et 2 sont pareils
    if($password != $passwordTwo) {
        header("location: inscription.php?error=1&message=Vos mots de passe doivent correspondre.");
    }
// Vérifier que la longueur est supérieur à 8
    if (strlen($password) < 8) {
        header("location: inscription.php?error=1&message=Votre mot de passe doit faire plus de 8 caractères");
    }
// Vérifier que l'email n'est pas un doublon
    $dbEmailVerification = $db->prepare("SELECT COUNT(*) AS numberEmail FROM user WHERE email = ?");
    $dbEmailVerification->execute([$email]);

    while($verifEmail = $dbEmailVerification->fetch()) {
        if($verifEmail['numberEmail'] != 0) {
            header('location: inscription.php?error=1&message=Email déjà utilisée ! Essayez un autre.');
            exit();
        }
    }

// Crypter le password
    $cryptPassword = "kj?;+".sha1($password)."+!-;,";
// Créer le secret, basé sur le mail crypté
    $secret = sha1($email).time();
    $secret = sha1($secret).time();

// Préparer la requête pour envoyer les données. Ce code est accessible une fois toutes les conditions effectuées.
    $finalRequest = $db->prepare("INSERT INTO user(email, password, secret) VALUE(?,?,?)");
    $finalRequest->execute([
            $email,
            $password,
            $secret,
    ]);
// Afficher le message à l'utilisateur
    header("location: inscription.php?success=1&message=Le compte a été créé. Veuillez vous connecter.");
}
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Netflix</title>
        <link rel="stylesheet" type="text/css" href="design/default.css">
        <link rel="icon" type="image/pngn" href="assets/favicon.png">
    </head>
    <body>

        <?php require_once('src/header.php'); ?>

        <section>
            <div id="login-body">
                <h1>S'inscrire</h1>

                <?php
                    if(isset($_GET['error']) && isset($_GET['message'])) {
                        echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
                    } else if (isset($_GET['success'])) {
                        echo '< class="alert success">Vous êtes désormais inscrits. <a href="index.php">Connectez vous</a></div>';
                    }
                ?>

                <form method="post" action="inscription.php">
                    <input type="email" name="email" placeholder="Votre adresse email" required />
                    <input type="password" name="password" placeholder="Mot de passe" required />
                    <input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
                    <button type="submit">S'inscrire</button>
                </form>

                <p class="grey">Déjà sur Netflix ? <a href="index.php">Connectez-vous</a>.</p>
            </div>
        </section>

        <?php require_once('src/footer.php'); ?>
    </body>
</html>