<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Building families</h2>

        <form method="post">

            <label for="name">Name</label>
            <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="255" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>building/families" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>