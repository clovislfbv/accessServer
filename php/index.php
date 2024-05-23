<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/index.css">
    <script src="../js/jquery.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="../js/script_website.js" type="module"></script>
    <script src="../js/script_index.js" type="module"></script>
    <title>Connectez-vous à votre serveur</title>
</head>
<body>
    <div class="main">
        <div class="left"></div>
        <div class="center">
            <h1 class="name">accessServer</h1>
            <div class="card">
                <div class="card-body">
                    <h3 class="description">Connectez-vous à votre serveur distant</h3>
                    <div class="component">
                        <form id="form" action="add_info_to_session.php" method="post">
                            <div class="host">
                                <label for="host">Adresse du serveur</label>
                                <input type="text" name="host" id="host" required>
                            </div>
                            <div class="user">
                                <label for="user">Nom d'utilisateur</label>
                                <input type="text" name="user" id="user" required>
                            </div>
                            <div class="password">
                                <label for="password">Mot de passe</label>
                                <input type="password" name="password" id="password" required>
                            </div>
                            <div class="port">
                                <label for="port">Port</label>
                                <input type="port" name="port" id="port" placeholder="22">
                            </div>
                            <input type="submit" class="submit_btn" value="Se connecter">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="right"></div>
    </div>
</body>
</html>