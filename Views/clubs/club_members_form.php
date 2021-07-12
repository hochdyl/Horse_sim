<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Club members</h2>

        <form method="post">

            <label for="club_id">Club ID</label>
            <input class="form-control" type="number" name="club_id" id="club_id" placeholder="Club ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="player_id">Player ID</label>
            <input class="form-control" type="number" name="player_id" id="player_id" placeholder="Player ID" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>club/members" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>