<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Club tournaments rewards</h2>

        <form method="post">

            <label for="club_tournament_id">Club tournament ID</label>
            <input class="form-control" type="number" name="club_tournament_id" id="club_tournament_id" placeholder="Club tournament ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="item_id">Item ID</label>
            <input class="form-control" type="number" name="item_id" id="item_id" placeholder="Item ID" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="quantity">Quantity</label>
            <input class="form-control" type="number" name="quantity" id="quantity" placeholder="Quantity" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <label for="obtention_rank">Obtention rank</label>
            <input class="form-control" type="number" name="obtention_rank" id="obtention_rank" placeholder="Obtention rank" value="<?php if (isset($data)): echo $data[3]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>club/tournament/rewards" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>