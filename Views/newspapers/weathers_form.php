<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Weathers</h2>

        <form method="post">

            <label for="name">Name</label>
            <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="255" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="description">Description</label>
            <input class="form-control" type="text" name="description" id="description" placeholder="Description" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="image">Image</label>
            <input class="form-control" type="text" name="image" id="image" placeholder="Image" maxlength="255" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>weathers" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>