<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Bank account history</h2>

        <form method="post">

            <label for="bank_account_id">Bank Account ID</label>
            <input class="form-control" type="number" name="bank_account_id" id="bank_account_id" placeholder="Bank Account ID" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="action">Action</label>
            <input class="form-control" type="number" name="action" id="action" placeholder="Action" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for=amount">Amount</label>
            <input class="form-control" type="text" name="amount" id="amount" placeholder="Amount" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <label for=label">Label</label>
            <input class="form-control" type="text" name="label" id="label" placeholder="Label" maxlength="255" value="<?php if (isset($data)): echo $data[3]; endif; ?>" required>
            <label for=date">Date</label>
            <input class="form-control" type="text" name="date" id="date" placeholder="Date" value="<?php if (isset($data)): echo $data[4]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>bank/history" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>