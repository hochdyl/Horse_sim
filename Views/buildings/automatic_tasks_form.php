<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Building automatic tasks</h2>

        <form method="post">

            <label for="automatic_task_action_id">Automatic task action ID</label>
            <input class="form-control" type="number" name="automatic_task_action_id" id="automatic_task_action_id" placeholder="Automatic task action ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="stable_building_id">Stable building ID</label>
            <input class="form-control" type="number" name="stable_building_id" id="stable_building_id" placeholder="Stable building ID" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="item_id">Item ID</label>
            <input class="form-control" type="number" name="item_id" id="item_id" placeholder="Item ID" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <label for="frequency">Frequency</label>
            <input class="form-control" type="number" name="frequency" id="frequency" placeholder="Frequency" value="<?php if (isset($data)): echo $data[3]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>automatic" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>