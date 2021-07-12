<main class="container-md">
    <section class="mb-3">
        <!-- Titre -->
        <h2 class="box-title hr">Tables</h2>
        <section class="tables-container">
            <?php if (permissions("players", $tables) || permissions("player_horses", $tables)): ?>
                <section class="table">
                    <header>
                        <img src="<?= SCRIPTS . 'images/horse.png' ?>" class="table-icon"><h1>Players</h1>
                    </header>
                    <?php if (permissions("players", $tables)): ?>
                        <a href="<?= ROOT ?>players" class="link">Players</a>
                    <?php endif; ?>
                    <?php if (permissions("player_horses", $tables)): ?>
                        <a href="<?= ROOT ?>player/horses" class="link">Player Horses</a>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <?php if (permissions("stables", $tables) || permissions("stable_buildings", $tables)): ?>
                <section class="table">
                    <header>
                        <img src="<?= SCRIPTS . 'images/horse.png' ?>" class="table-icon"><h1>Stables</h1>
                    </header>
                    <?php if (permissions("stables", $tables)): ?>
                        <a href="<?= ROOT ?>stables" class="link">Stables</a>
                    <?php endif; ?>
                    <?php if (permissions("stable_buildings", $tables)): ?>
                        <a href="<?= ROOT ?>stable/buildings" class="link">Stable buldings</a>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <?php if (permissions("newspapers", $tables) || permissions("news", $tables) ||
                permissions("newspaper_ads", $tables) || permissions("upcoming_events", $tables) ||
                permissions("ads", $tables) || permissions("weathers", $tables)): ?>
                <section class="table">
                    <header>
                        <img src="<?= SCRIPTS . 'images/horse.png' ?>" class="table-icon"><h1>Newspapers</h1>
                    </header>
                    <?php if (permissions("newspapers", $tables)): ?>
                        <a href="<?= ROOT ?>newspapers" class="link">Newspapers</a>
                    <?php endif; ?>
                    <?php if (permissions("news", $tables)): ?>
                        <a href="<?= ROOT ?>newspapers/news" class="link">News</a>
                    <?php endif; ?>
                    <?php if (permissions("newspaper_ads", $tables)): ?>
                        <a href="<?= ROOT ?>newspapers/ads" class="link">Newspapers ads</a>
                    <?php endif; ?>
                    <?php if (permissions("upcoming_events", $tables)): ?>
                        <a href="<?= ROOT ?>newspapers/upcoming" class="link">Upcoming events</a>
                    <?php endif; ?>
                    <?php if (permissions("ads", $tables)): ?>
                        <a href="<?= ROOT ?>ads" class="link">Ads</a>
                    <?php endif; ?>
                    <?php if (permissions("weathers", $tables)): ?>
                        <a href="<?= ROOT ?>weathers" class="link">Weathers</a>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <?php if (permissions("items", $tables) || permissions("item_types", $tables)): ?>
                <section class="table">
                    <header>
                        <img src="<?= SCRIPTS . 'images/horse.png' ?>" class="table-icon"><h1>Items</h1>
                    </header>
                    <?php if (permissions("items", $tables)): ?>
                        <a href="<?= ROOT ?>items" class="link">Items</a>
                    <?php endif; ?>
                    <?php if (permissions("item_types", $tables)): ?>
                        <a href="<?= ROOT ?>items/types" class="link">Items types</a>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <?php if (permissions("horses", $tables) || permissions("horse_breeds", $tables) ||
            permissions("horse_items", $tables) || permissions("horse_status", $tables) ||
            permissions("statuses", $tables)): ?>
                <section class="table">
                    <header>
                        <img src="<?= SCRIPTS . 'images/horse.png' ?>" class="table-icon"><h1>Horses</h1>
                    </header>
                    <?php if (permissions("horses", $tables)): ?>
                        <a href="<?= ROOT ?>horses" class="link">Horses</a>
                    <?php endif; ?>
                    <?php if (permissions("horse_breeds", $tables)): ?>
                        <a href="<?= ROOT ?>horse/breeds" class="link">Horse breeds</a>
                    <?php endif; ?>
                    <?php if (permissions("horse_items", $tables)): ?>
                        <a href="<?= ROOT ?>horse/items" class="link">Horse items</a>
                    <?php endif; ?>
                    <?php if (permissions("horse_status", $tables)): ?>
                        <a href="<?= ROOT ?>horse/status" class="link">Horse status</a>
                    <?php endif; ?>
                    <?php if (permissions("statuses", $tables)): ?>
                        <a href="<?= ROOT ?>statuses" class="link">Statuses</a>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <?php if (permissions("clubs", $tables) || permissions("club_buildings", $tables) ||
            permissions("club_items", $tables) || permissions("club_members", $tables) ||
            permissions("club_tournaments", $tables) || permissions("club_tournament_registrants", $tables) ||
            permissions("club_tournament_rewards", $tables)): ?>
                <section class="table">
                    <header>
                        <img src="<?= SCRIPTS . 'images/horse.png' ?>" class="table-icon"><h1>Clubs</h1>
                    </header>
                    <?php if (permissions("clubs", $tables)): ?>
                        <a href="<?= ROOT ?>clubs" class="link">Clubs</a>
                    <?php endif; ?>
                    <?php if (permissions("club_buildings", $tables)): ?>
                        <a href="<?= ROOT ?>club/buildings" class="link">Club buildings</a>
                    <?php endif; ?>
                    <?php if (permissions("club_items", $tables)): ?>
                        <a href="<?= ROOT ?>club/items" class="link">Club items</a>
                    <?php endif; ?>
                    <?php if (permissions("club_members", $tables)): ?>
                        <a href="<?= ROOT ?>club/members" class="link">Club members</a>
                    <?php endif; ?>
                    <?php if (permissions("club_tournaments", $tables)): ?>
                        <a href="<?= ROOT ?>club/tournaments" class="link">Club tournaments</a>
                    <?php endif; ?>
                    <?php if (permissions("club_tournament_registrants", $tables)): ?>
                        <a href="<?= ROOT ?>club/tournament/registrations" class="link">Club tournaments registrations</a>
                    <?php endif; ?>
                    <?php if (permissions("club_tournament_rewards", $tables)): ?>
                        <a href="<?= ROOT ?>club/tournament/rewards" class="link">Club tournaments rewards</a>
                <?php endif; ?>
                </section>
            <?php endif; ?>
            <?php if (permissions("buildings", $tables) || permissions("building_families", $tables) ||
            permissions("building_items", $tables) || permissions("automatic_tasks", $tables) ||
            permissions("automatic_task_actions", $tables) || permissions("weathers", $tables)): ?>
                <section class="table">
                    <header>
                        <img src="<?= SCRIPTS . 'images/horse.png' ?>" class="table-icon"><h1>Buildings</h1>
                    </header>
                    <?php if (permissions("buildings", $tables)): ?>
                        <a href="<?= ROOT ?>buildings" class="link">Buildings</a>
                    <?php endif; ?>
                    <?php if (permissions("building_families", $tables)): ?>
                        <a href="<?= ROOT ?>building/families" class="link">Building families</a>
                    <?php endif; ?>
                    <?php if (permissions("building_items", $tables)): ?>
                        <a href="<?= ROOT ?>building/items" class="link">Building items</a>
                    <?php endif; ?>
                    <?php if (permissions("building_types", $tables)): ?>
                        <a href="<?= ROOT ?>building/types" class="link">Building types</a>
                    <?php endif; ?>
                    <?php if (permissions("automatic_tasks", $tables)): ?>
                        <a href="<?= ROOT ?>automatic" class="link">Automatic tasks</a>
                    <?php endif; ?>
                    <?php if (permissions("automatic_task_actions", $tables)): ?>
                        <a href="<?= ROOT ?>automatic/actions" class="link">Automatic tasks actions</a>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            <?php if (permissions("bank_accounts", $tables) || permissions("bank_account_history", $tables)): ?>
                <section class="table">
                    <header>
                        <img src="<?= SCRIPTS . 'images/horse.png' ?>" class="table-icon"><h1>Bank accounts</h1>
                    </header>
                    <?php if (permissions("bank_accounts", $tables)): ?>
                        <a href="<?= ROOT ?>bank" class="link">Bank accounts</a>
                    <?php endif; ?>
                    <?php if (permissions("bank_account_history", $tables)): ?>
                        <a href="<?= ROOT ?>bank/history" class="link">Bank account history</a>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </section>
    </section>
</main>