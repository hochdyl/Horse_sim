<main class="container-md">
    <section class="box mb-3">
        <!-- Titre -->
        <h1 class="box-title">Documentation</h1>
        <hr>
        <h2 class="mb-4">Bienvenue sur HotHotHot !</h2>
        <h3><u>Création de votre compte</u></h3>
        <p>Vous pouvez créer votre propre compte via l'onglet en haut à droite 'compte', sur 's'inscrire'. Vous pouvez vous connecter avec votre compte Google. Une fois votre inscription terminé, il vous sera demandé de confirmer votre adresse e-mail.</p>
        <h3 class="mt-4"><u>Se connecter</u></h3>
        <p>Une fois la confirmation de votre e-mail, vous pouvez vous connecter sur le site internet avec votre e-mail et mot de passe. Si vous avez oublié votre mot de passe, vous pouvez le récuperer grâce avec votre adresse e-mail que vous avez utilisé lors de votre inscription.</p>
        <h3 class="mt-4"><u>Modification du compte</u></h3>
        <p>Vous êtes libre concernant votre compte, vous pouvez changer votre photo de profil, votre nom et prénom, votre mot de passe et adresse e-mail. Vous pouvez vous déconnecter du site internet et également supprimer votre compte à tout moment dans l'onglet 'Mon profil'</p>
        <h3 class="mt-4"><u>Accueil</u></h3>
        <p>L'accueil vous permet de suivre la température des capteurs intérieur et extérieur.</p>
        <h3 class="mt-4"><u>Paramétrages</u></h3>
        <p>L'onglet paramétrages va vous permettre de créer, modifier et supprimer vos alertes en fonction des capteurs. Il est également possible de modifier le nombre de valeur que vous souhaitez afficher dans l'accueil.</p>
        <h3 class="mt-4"><u>Documentation</u></h3>
        <p>L'onglet documentation vous permet de comprendre le fonctionnement du site internet et également de créer un fichier à l'aide d'un éditeur de texte.</p>
    </section>

    <?php if(isAuthenticated()):?>
        <section class="box mb-3">
            <!-- Titre -->
            <h1 class="box-title">Framework</h1>
            <hr>
            <h3><u>MVC</u></h3>
            <p>Ce site internet a été réalisé à l'aide d'un motif d'architecture logicielle appelé Modèle-Vue-Contrôleur (MVC).
                C'est une façon d'organiser une interface graphique d'un programme. Elle consiste à distinguer trois entités distinctes.
            </p>
            <p>
                <u>Le modèle :</u> Le modèle contient les données manipulées par le programme. Il assure la gestion de ces données et garantit leur intégrité. Dans le cas typique d'une base de données, c'est le modèle qui la contient.<br>
                <u>La vue :</u> La vue fait l'interface avec l'utilisateur. Sa première tâche est d'afficher les données qu'elle a récupérées auprès du modèle. Sa seconde tâche est de recevoir tous les actions de l'utilisateur (clic de souris, sélection d'une entrées, boutons, …). Ses différents événements sont envoyés au contrôleur.<br>
                <u>Le contrôleur :</u> Le contrôleur est chargé de la synchronisation du modèle et de la vue. Il reçoit tous les événements de l'utilisateur et enclenche les actions à effectuer. Si une action nécessite un changement des données, le contrôleur demande la modification des données au modèle et ensuite avertit la vue que les données ont changé pour que celle-ci se mette à jour. Certains événements de l'utilisateur ne concerne pas les données mais la vue. Dans ce cas, le contrôleur demande à la vue de se modifier.<br>
            </p>
            <h3 class="mt-4"><u>README Interne</u></h3>
            <ul>
                <li>Récupérer la base de données !</li>
                <li>Le fichier config.ini se présente maintenant de la façon suivante : (La variable "root_path" correspond au placement du site sur le serveur que tu utilises, '/' étant la racine du serveur)</li>
                <li>Faire fonctionner la fonction mail() de PHP en local sous wamp : <a href="https://grafikart.fr/blog/mail-local-wamp">MAIL</a></li>
                <li>Télécharger Google lib</li>
            </ul>
            <h3 class="mt-4"><u>Réalisations</u></h3>
            <ul>
                <li>Fichier de configuration</li>
                <li>Système de cache</li>
                <li>Session</li>
                <li>Récupération des données capteurs</li>
                <li>Paramétrages alertes</li>
                <li>Connexion/inscription + API Google</li>
                <li>Documentation + éditeur wisiwig</li>
                <li>Mentions légales et CGU</li>
                <li>Antispam et antibot</li>
                <li>Vérification de compte avec envoie de mail</li>
            </ul>
        </section>
    <?php
    endif;
    ?>

    <section class="box">
        <!-- Titre -->
        <h1 class="box-title">Editeur</h1>
        <hr>
        <form method="post" action="">
            <textarea name="documentation" id="editor"></textarea>
            <button type="submit">Valider</button>
        </form>
    </section>
</main>