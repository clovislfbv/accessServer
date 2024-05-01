<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/jquery.js"></script>
    <script src="../js/script_website.js" type="module"></script>
    <title>Connectez-vous à votre serveur</title>
</head>
<body>
    <form action="result.php" method="post">
        <label for="host">Adresse du serveur</label>
        <input type="text" name="host" id="host" required>
        <label for="user">Nom d'utilisateur</label>
        <input type="text" name="user" id="user" required>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required>
        <label for="port">port</label>
        <input type="port" name="port" id="port" placeholder="22">
        <input type="submit" class="submit_btn" value="Se connecter">
    </form>
</body>
</html>