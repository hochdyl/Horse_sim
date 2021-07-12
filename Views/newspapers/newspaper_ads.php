<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Newspaper ads</h2>

        <?php if (permissions("SELECT", $permissions)): ?>
            <form class="search-container">
                <input class="input" name="search" type="text" value="<?= $search ?: "" ?>" placeholder="Votre recherche">
                <input class="submit" type="submit" value="Rechercher">
            </form>
        <?php endif; ?>

        <form method="post">

            <div class="tbl-header">
                <table cellpadding="0" cellspacing="0" border="0">
                    <thead>
                        <?php if (permissions("SELECT", $permissions)): ?>
                            <tr>
                                <?php if (permissions("DELETE", $permissions)): ?>
                                    <th class="cw-45 checkbox"><input type="checkbox" id="select-all"></th>
                                <?php endif; ?>
                                <th><a href="<?= ROOT ?>newspapers/ads?page=1<?= $search ? "&search=$search" : "" ?>&filter=newspaper_id<?= $filter == "newspaper_id" && $order == "DESC" ? "&order=ASC" : "&order=DESC"?>">Newspaper</a>
                                    <?php
                                    if ($filter == "newspaper_id" && $order == "DESC") echo "<img class='arrow' src='".SCRIPTS."images/down-arrow.svg'>";
                                    else if ($filter == "newspaper_id" && $order == "ASC") echo "<img class='arrow' src='".SCRIPTS."images/up-arrow.svg'>";
                                    ?>
                                </th>
                                <th><a href="<?= ROOT ?>newspapers/ads?page=1<?= $search ? "&search=$search" : "" ?>&filter=ad_id<?= $filter == "ad_id" && $order == "DESC" ? "&order=ASC" : "&order=DESC"?>">Ad</a>
                                    <?php
                                    if ($filter == "ad_id" && $order == "DESC") echo "<img class='arrow' src='".SCRIPTS."images/down-arrow.svg'>";
                                    else if ($filter == "ad_id" && $order == "ASC") echo "<img class='arrow' src='".SCRIPTS."images/up-arrow.svg'>";
                                    ?>
                                </th>
                                <?php if (permissions("UPDATE", $permissions)): ?>
                                    <th class="cw-100 action">Action</th>
                                <?php endif; ?>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Permissions insuffisantes.</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                </table>
            </div>
            <div class="tbl-content">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                        <?php if (permissions("SELECT", $permissions)): ?>
                            <?php foreach ($data as $row) : ?>
                                <tr>
                                    <?php if (permissions("DELETE", $permissions)): ?>
                                        <td class="cw-45 checkbox"><input type="checkbox" name="row[]" value="<?= $row['newspaper_id']."-".$row['ad_id'] ?>"></td>
                                    <?php endif; ?>
                                    <td><?= $row['newspaper_id'] ?></td>
                                    <td><?= $row['ad_id'] ?></td>
                                    <?php if (permissions("UPDATE", $permissions)): ?>
                                        <td class="cw-100 action"><a href="<?= ROOT ?>newspapers/ads/form?newspaper_id=<?= $row['newspaper_id'] ?>&ad_id=<?= $row['ad_id'] ?>"><input type="button" value="Editer"></a></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="actions-container">
                <div>
                    <?php if (permissions("INSERT", $permissions)): ?>
                        <a href="<?= ROOT ?>newspapers/ads/form"><input type="button" name="add" value="Ajouter"></a>
                    <?php endif; ?>
                    <?php if (permissions("DELETE", $permissions)): ?>
                        <input type="submit" name="delete" value="Supprimer">
                    <?php endif; ?>
                </div>
                <?php if (permissions("SELECT", $permissions)): ?>
                    <div class="pages-container">
                        <a href="<?= ROOT ?>newspapers/ads?page=1<?= $search ? "&search=$search" : "" ?><?= $filter ? "&filter=$filter" : "" ?><?= $order ? "&order=$order" : "" ?>"><input <?= $current_page == 1 ? "class='active'" : "" ?> type="button" value="1"></a>
                        <?php $i = 2 ?>
                        <?php if($current_page - 3 > 1) : ?>
                            <?php $i = $current_page - 2 ?>
                            <p>...</p>
                        <?php endif; ?>
                        <?php if($last_page > 1) : ?>
                            <?php for ($i; $i<=$last_page; $i++) : ?>
                                <?php if($i >= $current_page + 3) : ?>
                                    <?php if($current_page + 3 < $last_page) : ?>
                                        <p>...</p>
                                    <?php endif; ?>
                                    <a href="<?= ROOT ?>newspapers/ads?page=<?= $last_page ?><?= $search ? "&search=$search" : "" ?><?= $filter ? "&filter=$filter" : "" ?><?= $order ? "&order=$order" : "" ?>">
                                        <input <?= $current_page == $last_page ? "class='active'" : "" ?> type="button" value="<?= $last_page ?>">
                                    </a>
                                    <?php break; ?>
                                <?php endif; ?>
                                <a href="<?= ROOT ?>newspapers/ads?page=<?= $i ?><?= $search ? "&search=$search" : "" ?><?= $filter ? "&filter=$filter" : "" ?><?= $order ? "&order=$order" : "" ?>">
                                    <input <?= $i == $current_page ? "class='active'" : "" ?> type="button" value="<?= $i ?>">
                                </a>
                            <?php endfor; ?>
                        <?php endif ?>
                        <form>
                            <input class="page-input" type="number" min="1" name="page" placeholder="Page">
                        </form>
                    </div>
                <?php endif ?>
            </div>
        </form>

    </section>
</main>

<?php if (permissions("SELECT", $permissions)): ?>
    <script type="text/javascript">
        $('#select-all').click(function(event) {
            if(this.checked) {
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        });
    </script>
<?php endif ?>