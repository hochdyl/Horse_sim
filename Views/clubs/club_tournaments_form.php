<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Club tournaments</h2>

        <form method="post">

            <label for="club_id">Club ID</label>
            <input class="form-control" type="number" name="club_id" id="club_id" placeholder="Club ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="name">Name</label>
            <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="255" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="start_date">Start date</label>
            <input class="form-control" type="text" name="start_date" id="start_date" placeholder="Start date" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <label for="end_date">End date</label>
            <input class="form-control" type="text" name="end_date" id="end_date" placeholder="End date" value="<?php if (isset($data)): echo $data[3]; endif; ?>" required>
            <label for="base_registration_fee">Base registration fee</label>
            <input class="form-control" type="text" name="base_registration_fee" id="base_registration_fee" placeholder="Base registration fee" value="<?php if (isset($data)): echo $data[4]; endif; ?>" required>
            <label for="member_registration_fee">Member registration fee</label>
            <input class="form-control" type="text" name="member_registration_fee" id="member_registration_fee" placeholder="Member registration fee" value="<?php if (isset($data)): echo $data[5]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>club/tournaments" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>