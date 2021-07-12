<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Horses</h2>

        <form method="post">

            <label for="name">Name</label>
            <input class="form-control" type="text" name="name" id="name" placeholder="Name" maxlength="50" value="<?php if (isset($data)): echo $data[0]; endif; ?>" autofocus required>
            <label for="breed_id">Breed ID</label>
            <input class="form-control" type="number" name="breed_id" id="breed_id" placeholder="Breed ID" value="<?php if (isset($data)): echo $data[1]; endif; ?>" required>
            <label for="health">Health</label>
            <input class="form-control" type="number" name="health" id="health" placeholder="Health" value="<?php if (isset($data)): echo $data[2]; endif; ?>" required>
            <label for="fatigue">Fatigue</label>
            <input class="form-control" type="number" name="fatigue" id="fatigue" placeholder="Fatigue" value="<?php if (isset($data)): echo $data[3]; endif; ?>" required>
            <label for="morale">Morale</label>
            <input class="form-control" type="number" name="morale" id="morale" placeholder="Morale" value="<?php if (isset($data)): echo $data[4]; endif; ?>" required>
            <label for="stress">Stress</label>
            <input class="form-control" type="number" name="stress" id="stress" placeholder="Stress" value="<?php if (isset($data)): echo $data[5]; endif; ?>" required>
            <label for="hunger">Hunger</label>
            <input class="form-control" type="number" name="hunger" id="hunger" placeholder="Hunger" value="<?php if (isset($data)): echo $data[6]; endif; ?>" required>
            <label for="cleanliness">Cleanliness</label>
            <input class="form-control" type="number" name="cleanliness" id="cleanliness" placeholder="Cleanliness" value="<?php if (isset($data)): echo $data[7]; endif; ?>" required>
            <label for="global_condition">Global condition</label>
            <input class="form-control" type="number" name="global_condition" id="global_condition" placeholder="Global condition" value="<?php if (isset($data)): echo $data[8]; endif; ?>" required>
            <label for="resistance">Resistance</label>
            <input class="form-control" type="number" name="resistance" id="resistance" placeholder="Resistance" value="<?php if (isset($data)): echo $data[9]; endif; ?>" required>
            <label for="stamina">Stamina</label>
            <input class="form-control" type="number" name="stamina" id="stamina" placeholder="Stamina" value="<?php if (isset($data)): echo $data[10]; endif; ?>" required>
            <label for="jump">Jump</label>
            <input class="form-control" type="number" name="jump" id="jump" placeholder="Jump" value="<?php if (isset($data)): echo $data[11]; endif; ?>" required>
            <label for="speed">Speed</label>
            <input class="form-control" type="number" name="speed" id="speed" placeholder="Speed" value="<?php if (isset($data)): echo $data[12]; endif; ?>" required>
            <label for="sociability">Sociability</label>
            <input class="form-control" type="number" name="sociability" id="sociability" placeholder="Sociability" value="<?php if (isset($data)): echo $data[13]; endif; ?>" required>
            <label for="intelligence">Intelligence</label>
            <input class="form-control" type="number" name="intelligence" id="intelligence" placeholder="Intelligence" value="<?php if (isset($data)): echo $data[14]; endif; ?>" required>
            <label for="temperament">Temperament</label>
            <input class="form-control" type="number" name="temperament" id="temperament" placeholder="Temperament" value="<?php if (isset($data)): echo $data[15]; endif; ?>" required>
            <label for="experience">Experience</label>
            <input class="form-control" type="number" name="experience" id="experience" placeholder="Experience" value="<?php if (isset($data)): echo $data[16]; endif; ?>" required>
            <label for="level">Level</label>
            <input class="form-control" type="number" name="level" id="level" placeholder="Level" value="<?php if (isset($data)): echo $data[17]; endif; ?>" required>
            <hr>
            <?php if (isset($data)): ?>
                <button type="submit" id="update" name="update">Modifier</button>
            <?php else: ?>
                <button type="submit" id="insert" name="insert">Ajouter</button>
            <?php endif; ?>
            <a href="<?= ROOT ?>horses" class="no-decoration"><button type="button">Retour</button></a>

        </form>

    </section>
</main>