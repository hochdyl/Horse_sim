<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Horse status</h2>

        <form method="post">

            <label for="horse_id">Horse ID</label>
            <input class="form-control" type="number" name="horse_id" id="horse_id" placeholder="Horse ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="status_id">Status ID</label>
            <input class="form-control" type="number" name="status_id" id="status_id" placeholder="Status ID" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="onset_date">Onset date</label>
            <input class="form-control" type="text" name="onset_date" id="onset_date" placeholder="Onset date" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>horse/status" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>