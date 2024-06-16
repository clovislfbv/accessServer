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
            <div class="card menu">
                <div class="card-body">
                    <h3 class="description">Connectez-vous à votre serveur distant</h3>
                    <div class="component">
                        <div class="left_card">
                            <input type="radio" class="with_password" checked>
                            <input type="radio" class="with_pubfile">
                        </div>
                        <div class="center_card">
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
                                    <div class="password_zone">
                                        <label for="password">Mot de passe</label>
                                    </div>
                                    <input type="password" name="password" id="password" required>
                                </div>
                                <div class="pubfile">
                                    <div class="pubfile_zone">
                                        <label for="pubfile">Fichier de clé publique</label>
                                    </div>
                                    <input type="file" name="pubfile" id="pubfile" accept=".pub">
                                </div>
                                <div class="privfile">
                                    <div class="privfile_zone">
                                        <label for="privfile">Fichier de clé privée</label>
                                    </div>
                                    <input type="file" name="privfile" id="privfile">
                                </div>
                                <div class="password_key">
                                    <div class="password_key_zone">
                                        <input type="checkbox" name="with_password_key" id="with_password_key">
                                        <label for="password_key">Mot de passe de la clé privée</label>
                                    </div>
                                    <input type="password" name="password_key" id="password_key">
                                </div>
                                <div class="port">
                                    <label for="port">Port</label>
                                    <input type="port" name="port" id="port" placeholder="22">
                                </div>
                                <input type="hidden" name="id_choice" class="id_choice" value="1"></input>
                                <input type="submit" class="submit_btn" value="Se connecter">
                            </form>
                        </div>
                        <div class="right_card"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="right"></div>
    </div>
</body>
</html>