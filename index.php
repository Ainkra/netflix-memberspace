<?php
//#############################
//          DATABASE
//#############################

require './src/connection.php';
global $db;

//#############################
//      CONNECTION MANAGER
//#############################
// Démarrer la session
session_start();

// Vérifier si le formulaire de connexion à bien été envoyée
if (!empty($_POST['email']) && !empty($_POST['password'])) {
// Protéger nos variables (récupérer)
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

// Vérifier si l'adresse mail est valide
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location: index.php?error=1&message='Votre email est invalide. (Ex: exemple@gmail.com'");
    }

// Vérifier si l'adresse mail existe dans la base de donnée ?
    $emailIsValid = $db->prepare("SELECT * FROM user WHERE email = ?");
    $emailIsValid->execute([$email]);

    if($result = $emailIsValid->fetch()) {

        // Connexion
        $cryptPassword = "kj?;+".sha1($password)."+!-;,";

        $req = $db->prepare("SELECT * FROM user WHERE email = ? AND password = ?");
        $req->execute([$email, $cryptPassword]);

        try {
            // Fetch récupère une ligne dans la bdd (une row)
            if($user = $req->fetch()) {

                $_SESSION["connect"] = 1;
                $_SESSION['email'] = $user['email'];
                header('location: index.php?success=1');
                exit();
            } else {
                header("location: index.php?error=1&message='Le mot de passe ou l'email est invalide. Veuillez réessayer.'");
                exit();
            }

        } catch (Exception $e) {
            echo 'Erreur: '.$e->getMessage();
        }

    } else {
        header("location: index.php?error=1&message='Email ou mot de passe invalide. Veuillez réessayer.'");
    }
}
?>

<html lang="fr">
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

        <?php if(isset($_SESSION['connect'])) { ?>

            <h1>Bonjour !</h1>
            <?php
            if(isset($_GET['success'])){
                echo'<div class="alert success">Vous êtes maintenant connecté.</div>';
            } ?>
            <p>Qu'allez-vous regarder aujourd'hui ?</p>
            <small><a href="logout.php">Déconnexion</a></small>

        <?php } else { ?>
            <h1>S'identifier</h1>

            <?php if(isset($_GET['error'])) {

                if(isset($_GET['message'])) {
                    echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
                }

            } ?>

            <form method="post" action="index.php">
                <label>
                    <input type="email" name="email" placeholder="Votre adresse email" required />
                </label>
                <label>
                    <input type="password" name="password" placeholder="Mot de passe" required />
                </label>
                <button type="submit">S'identifier</button>
                <label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
            </form>


            <p class="grey">Première visite sur Netflix ? <a href="inscription.php">Inscrivez-vous</a>.</p>
        <?php } ?>
    </div>
</section>

<?php require_once('src/footer.php'); ?>
</body>
</html>