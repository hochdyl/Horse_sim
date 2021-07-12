<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Club tournament registrations</h2>

        <form method="post">

            <label for="club_tournament_id">Club tournament ID</label>
            <input class="form-control" type="number" name="club_tournament_id" id="club_tournament_id" placeholder="Club tournament ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="player_id">Player ID</label>
            <input class="form-control" type="number" name="player_id" id="player_id" placeholder="Player ID" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="rank">Rank</label>
            <input class="form-control" type="number" name="rank" id="rank" placeholder="Rank" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>club/tournament/registrations" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>