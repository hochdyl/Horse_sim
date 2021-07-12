<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Items</h2>

        <form method="post">

            <label for="item_type_id">Item type ID</label>
            <input class="form-control" type="number" name="item_type_id" id="item_type_id" placeholder="Item type ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="description">Description</label>
            <input class="form-control" type="text" name="description" id="description" placeholder="Description" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="level">Level</label>
            <input class="form-control" type="number" name="level" id="level" placeholder="Level" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <label for="price">Price</label>
            <input class="form-control" type="text" name="price" id="price" placeholder="Price" value="<?php if (isset($data)): echo $data[3]; endif; ?>" required>
            <label for="on_sale">On sale</label>
            <input class="form-control" type="number" name="on_sale" id="on_sale" placeholder="On sale" max="1" min="0" value="<?php if (isset($data)): echo $data[4]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>items" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>