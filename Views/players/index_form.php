<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Players</h2>

        <form method="post">

            <label for="nickname">Nickname</label>
            <input class="form-control" type="text" name="nickname" id="nickname" placeholder="Nickname" maxlength="50" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="mail">Mail</label>
            <input class="form-control" type="text" name="mail" id="mail" placeholder="Mail" maxlength="255" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="password">Password</label>
            <input class="form-control" type="text" name="password" id="password" placeholder="Password" maxlength="255" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <label for="last_name">Last name</label>
            <input class="form-control" type="text" name="last_name" id="last_name" placeholder="Last name" maxlength="255" value="<?php if (isset($data)): echo $data[3]; endif; ?>" required>
            <label for="first_name">First name</label>
            <input class="form-control" type="text" name="first_name" id="first_name" placeholder="First name" maxlength="255" value="<?php if (isset($data)): echo $data[4]; endif; ?>" required>
            <label for="sexe">Sexe</label>
            <input class="form-control" type="text" name="sexe" id="sexe" placeholder="Sexe" maxlength="1" value="<?php if (isset($data)): echo $data[5]; endif; ?>" required>
            <label for="phone">Phone</label>
            <input class="form-control" type="text" name="phone" id="phone" placeholder="Phone" maxlength="15" value="<?php if (isset($data)): echo $data[6]; endif; ?>" required>
            <label for="street">Street</label>
            <input class="form-control" type="text" name="street" id="street" placeholder="Street" maxlength="255" value="<?php if (isset($data)): echo $data[7]; endif; ?>" required>
            <label for="city">City</label>
            <input class="form-control" type="text" name="city" id="city" placeholder="City" maxlength="255" value="<?php if (isset($data)): echo $data[8]; endif; ?>" required>
            <label for="zip_code">Zip code</label>
            <input class="form-control" type="text" name="zip_code" id="zip_code" placeholder="Zip code" maxlength="10" value="<?php if (isset($data)): echo $data[9]; endif; ?>" required>
            <label for="country">Country</label>
            <input class="form-control" type="text" name="country" id="country" placeholder="Country" maxlength="255" value="<?php if (isset($data)): echo $data[10]; endif; ?>" required>
            <label for="avatar">Avatar</label>
            <input class="form-control" type="text" name="avatar" id="avatar" placeholder="Avatar" maxlength="255" value="<?php if (isset($data)): echo $data[11]; endif; ?>" required>
            <label for="description">Description</label>
            <input class="form-control" type="text" name="description" id="description" placeholder="Description" value="<?php if (isset($data)): echo $data[12]; endif; ?>" required>
            <label for="website">Website</label>
            <input class="form-control" type="text" name="website" id="website" placeholder="Website" maxlength="255" value="<?php if (isset($data)): echo $data[13]; endif; ?>" required>
            <label for="ip_address">IP Address</label>
            <input class="form-control" type="text" name="ip_address" id="ip_address" placeholder="IP Address" maxlength="15" value="<?php if (isset($data)): echo $data[14]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>players" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>