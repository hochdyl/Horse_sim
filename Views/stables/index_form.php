<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Stables</h2>

        <form method="post">

            <label for="player_id">Player ID</label>
            <input class="form-control" type="number" name="player_id" id="player_id" placeholder="Player ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="buildings_limit">Buildings limit</label>
            <input class="form-control" type="number" name="buildings_limit" id="buildings_limit" placeholder="Buildings limit" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="price">Price</label>
            <input class="form-control" type="text" name="price" id="price" placeholder="Price" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <label for="on_sale">On sale</label>
            <input class="form-control" type="number" name="on_sale" id="on_sale" placeholder="On sale" value="<?php if (isset($data)): echo $data[3]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>stables" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>