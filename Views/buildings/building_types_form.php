<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Building types</h2>

        <form method="post">

            <label for="name">Name</label>
            <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="255" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="items_limit">Items limit</label>
            <input class="form-control" type="number" name="items_limit" id="items_limit" placeholder="Items limit" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="horses_limit">Horses limit</label>
            <input class="form-control" type="number" name="horses_limit" id="horses_limit" placeholder="Horses limit" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>building/types" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>