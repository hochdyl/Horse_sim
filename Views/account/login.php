<main class="container-fluid">
    <!-- Connexion -->
    <section class="box w-sm">
        <!-- Titre et redirection vers inscription -->
        <h1 class="box-title text-center">Connexion</h1>
        </p>
        <!-- Formulaire -->
        <form id="login_form" action="" method="post">
            <hr>
            <!-- Email -->
            <label for="username">Nom d'utilisateur</label>
            <input class="form-control" type="text" name="username" id="username" placeholder="Username" maxlength="50" autofocus required autocomplete="username">
            <!-- Mot de passe -->
            <label for="password">Mot de passe</label>
            <input class="form-control" type="password" name="password" id="password" placeholder="••••••••••••••" maxlength="99" required autocomplete="current-password">
            <hr>
            <!-- Se connecter -->
            <button type="submit" id="login" name="login">Se connecter</button>
        </form>
    </section>
</main>
