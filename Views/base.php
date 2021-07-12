<!doctype html>
<html lang="fr">
<head>
    <!-- Meta pour l'encodage et l'affichage mobile -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Titre de la page -->
    <title><?= $title ??= 'Projet' ?> | Simulateur</title>

    <!-- CSS -->
    <link rel="icon" href="<?= SCRIPTS . 'images/favicon.ico' ?>">
    <link rel="stylesheet" href="<?= SCRIPTS . 'css/bootstrap/bootstrap.min.css' ?>">
    <link rel="stylesheet" href="<?= SCRIPTS . 'css/style.css' ?>">

    <!-- Jquery insertion before template is loaded needed to use $ short code -->
    <?= addJavaScript('js/jquery/jquery-3.5.1.min.js') ?>
</head>

<body>
<!-- Haut de page -->
<header>
    <nav class="navbar navbar-expand-sm navbar-dark">
        <section class="container-xl">
            <a class="navbar-brand" href="<?= ROOT ?>">
                <img src="<?= SCRIPTS . 'images/logo.png' ?>" alt="Logo Hothothot.">
            </a>
            <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="navbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <hr class="mt-3 d-block d-sm-none">
                        <a class="nav-link" href="<?= ROOT ?>">Accueil</a>
                    </li>
                    <li class="nav-item dropdown d-block d-sm-none">
                        <hr class="mt-3">
                        <?php if (isAuthenticated()): ?>
                            <a class="nav-link" id="dropdown" href="" data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <?= "{$_SESSION['username']}" ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= ROOT ?>logout">Se déconnecter</a>
                                </li>
                            </ul>
                        <?php else: ?>
                            <a class="nav-link" id="dropdown" href="" data-bs-toggle="dropdown"
                               aria-expanded="false">Compte</a>
                            <ul class="dropdown-menu" aria-labelledby="dropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= ROOT ?>login">Se connecter</a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </li>
                </ul>
                <section class="nav-item dropdown d-none d-sm-block">
                    <?php if (isAuthenticated()): ?>
                        <a class="nav-link" id="dropdown" href="" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= "{$_SESSION['username']}" ?>
                            <img src="<?= SCRIPTS . 'images/profil-picture.png' ?>" alt="Profil picture.">
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown">
                            <li>
                                <a class="dropdown-item" href="<?= ROOT ?>logout">Se déconnecter</a>
                            </li>
                        </ul>
                    <?php else: ?>
                        <a class="nav-link" id="dropdown" href="" data-bs-toggle="dropdown" aria-expanded="false">
                            Compte
                            <img src="<?= SCRIPTS . 'images/profil-picture.png' ?>" alt="Profil picture.">
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown">
                            <li>
                                <a class="dropdown-item" href="<?= ROOT ?>login">Se connecter</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </section>
            </div>
        </section>
    </nav>
</header>

<!-- Contenu principal -->
<?= $content ??= null ?>

<!-- Bas de page -->
<footer>
    <section class="container">
        <p>
            &copy; <?= date("Y"); ?>
            <a href="<?= ROOT ?>">Horse_sim.fr</a>
        <p>
    </section>
</footer>

<!-- Scripts -->
<?= addJavaScript('js/bootstrap/bootstrap.min.js') ?>
<?= addJavaScript('js/tables.js') ?>

</body>
</html>
