<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\Classes\Validator;
use App\Core\System\Controller;
use App\Models\BuildingsModel;
use App\Models\PlayersModel;
use App\Models\Stable_BuildingsModel;
use App\Models\StablesModel;

final class StablesController extends Controller {

    #[Route('/stables', 'stables', ['GET', 'POST'])] public function index(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("stables", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("stables", $tables)) {
                    $position = array_search("stables", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $stables = new StablesModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $stables->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'stables');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($stables->countLike($search_string, ["id", "player_id", "building_limit"]));
                } else $nb_items = $stables->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $stables = $stables->find($search_string, ["id", "player_id", "building_limit"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($stables as $stable) {
                    $data[$i]['id'] = $stable->getId();
                    $data[$i]['player_id'] = $stable->getPlayerId();
                    $data[$i]['building_limit'] = $stable->getBuildingsLimit();
                    $i++;
                }

                $params = [
                    'data'=> $data,
                    'current_page'=> $current_page,
                    'last_page'=> $last_page,
                    'search'=> $search_string,
                    'permissions'=> $permissions,
                    'filter'=> $filter,
                    'order'=> $order,
                ];
            }

            $this->render(name_file: 'stables/index', params: $params, title: 'Stables');
        };
    }

    #[Route('/stables/form', 'stables_form', ['GET', 'POST'])] public function indexForm(Request $request)
    {
        $auth_table = "stables";
        $link_table = "stables";
        $this_table = "stables_form";
        $page_title = "Stables";
        $page_localisation = "stables/index_form";
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions($auth_table, $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array($auth_table, $tables)) {
                    $position = array_search($auth_table, $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
                if (!$this->permissions("INSERT", $permissions) && !$this->permissions("UPDATE", $permissions)) {
                    $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour ajouter ou modifier les données de cette table.");
                    $this->redirect(self::reverse($link_table));
                } else {
                    $validator = new Validator($_POST);
                    $stables = new StablesModel();
                    $players = new PlayersModel();

                    if ($request->get->get('id')) {
                        $account = $stables->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'player_id' => ['required'],
                                    'buildings_limit' => ['required'],
                                    'price' => ['required'],
                                    'on_sale' => ['required'],
                                ]);

                                if (!$players->findById($request->post->get('player_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $stables->setPlayerId($request->post->get('player_id'))
                                        ->setBuildingsLimit($request->post->get('buildings_limit'))
                                        ->setPrice($request->post->get('price'))
                                        ->setOnSale($request->post->get('on_sale'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getPlayerId();
                            $data[] = $account->getBuildingsLimit();
                            $data[] = $account->getPrice();
                            $data[] = $account->getOnSale();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'player_id' => ['required'],
                                'buildings_limit' => ['required'],
                                'price' => ['required'],
                                'on_sale' => ['required'],
                            ]);

                            if (!$players->findById($request->post->get('player_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $stables->setPlayerId($request->post->get('player_id'))
                                    ->setBuildingsLimit($request->post->get('buildings_limit'))
                                    ->setPrice($request->post->get('price'))
                                    ->setOnSale($request->post->get('on_sale'))
                                    ->create();

                                $this->addFlash('success', "Les données ont été ajouté dans la table.");
                                $this->redirect(self::reverse($link_table));
                            }
                        }

                        $this->render(name_file: $page_localisation, title: $page_title);
                    }
                }
            }
        }
    }

    #[Route('/stable/buildings', 'stable_buildings', ['GET', 'POST'])] public function stableBuildings(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("stable_buildings", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("stable_buildings", $tables)) {
                    $position = array_search("stable_buildings", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $stable_buildings = new Stable_BuildingsModel();

                if (isset($_POST['row'])) {
                    if (isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $stableid = $ids[0];
                            $buildingid = $ids[1];
                            $stable_buildings->query("DELETE FROM {$stable_buildings->getTableName()} WHERE stable_id = $stableid AND building_id = $buildingid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'stable/buildings');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if (isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($stable_buildings->countLike($search_string, ["stable_id", "building_id"]));
                } else $nb_items = $stable_buildings->countAll()->nb_items;
                if (isset($_GET['filter'])) $filter = $_GET['filter'];
                if (isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items / NB_PER_PAGE);
                $current_page = 1;
                if (isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if (isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $stable_buildings = $stable_buildings->find($search_string, ["stable_id", "building_id"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($stable_buildings as $stable_building) {
                    $data[$i]['id'] = $stable_building->getId();
                    $data[$i]['stable_id'] = $stable_building->getStableId();
                    $data[$i]['building_id'] = $stable_building->getBuildingId();
                    $i++;
                }

                $params = [
                    'data' => $data,
                    'current_page' => $current_page,
                    'last_page' => $last_page,
                    'search' => $search_string,
                    'permissions' => $permissions,
                    'filter' => $filter,
                    'order' => $order,
                ];
            }

            $this->render(name_file: 'stables/stable_buildings', params: $params, title: 'Stable buildings');
        };
    }

    #[Route('/stable/buildings/form', 'stable_buildings_form', ['GET', 'POST'])] public function stableBuildingsForm(Request $request)
    {
        $auth_table = "stable_buildings";
        $link_table = "stable_buildings";
        $this_table = "stable_buildings_form";
        $page_title = "Stable buildings";
        $page_localisation = "stables/stable_buildings_form";
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions($auth_table, $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array($auth_table, $tables)) {
                    $position = array_search($auth_table, $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
                if (!$this->permissions("INSERT", $permissions) && !$this->permissions("UPDATE", $permissions)) {
                    $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour ajouter ou modifier les données de cette table.");
                    $this->redirect(self::reverse($link_table));
                } else {
                    $validator = new Validator($_POST);
                    $stable_buildings = new Stable_BuildingsModel();
                    $stables = new StablesModel();
                    $buildings = new BuildingsModel();

                    if ($request->get->get('id')) {
                        $account = $stable_buildings->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'stable_id' => ['required'],
                                    'building_id' => ['required'],
                                ]);

                                if (!$stables->findById($request->post->get('stable_id')) &&
                                    !$buildings->findById($request->post->get('building_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $stable_buildings->setStableId($request->post->get('stable_id'))
                                        ->setBuildingId($request->post->get('building_id'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getStableId();
                            $data[] = $account->getBuildingId();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'stable_id' => ['required'],
                                'building_id' => ['required'],
                            ]);

                            if (!$stables->findById($request->post->get('stable_id')) &&
                                !$buildings->findById($request->post->get('building_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $stable_buildings->setStableId($request->post->get('stable_id'))
                                    ->setBuildingId($request->post->get('building_id'))
                                    ->create();

                                $this->addFlash('success', "Les données ont été ajouté dans la table.");
                                $this->redirect(self::reverse($link_table));
                            }
                        }

                        $this->render(name_file: $page_localisation, title: $page_title);
                    }
                }
            }
        }
    }
}
