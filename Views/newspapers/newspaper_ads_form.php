<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Newspaper ads</h2>

        <form method="post">

            <label for="newspaper_id">Newspaper ID</label>
            <input class="form-control" type="number" name="newspaper_id" id="newspaper_id" placeholder="Newspaper ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="ad_id">Ad ID</label>
            <input class="form-control" type="number" name="ad_id" id="ad_id" placeholder="Ad ID" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>newspapers/ads" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>