<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\Classes\Validator;
use App\Core\System\Controller;
use App\Models\BuildingsModel;
use App\Models\Club_BuildingsModel;
use App\Models\Club_ItemsModel;
use App\Models\Club_MembersModel;
use App\Models\Club_Tournament_RegistrantsModel;
use App\Models\Club_Tournament_RewardsModel;
use App\Models\Club_TournamentsModel;
use App\Models\ClubsModel;
use App\Models\ItemsModel;
use App\Models\PlayersModel;

final class ClubsController extends Controller {

    #[Route('/clubs', 'clubs', ['GET', 'POST'])] public function index(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("clubs", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("clubs", $tables)) {
                    $position = array_search("clubs", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $clubs = new ClubsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $clubs->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'clubs');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($clubs->countLike($search_string, ["id", "player_id", "buildings_limit", "membership_fee"]));
                } else $nb_items = $clubs->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $clubs = $clubs->find($search_string, ["id", "player_id", "buildings_limit", "membership_fee"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($clubs as $club) {
                    $data[$i]['id'] = $club->getId();
                    $data[$i]['player_id'] = $club->getPlayerId();
                    $data[$i]['buildings_limit'] = $club->getBuildingsLimit();
                    $data[$i]['membership_fee'] = $club->getMembershipFee();
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

            $this->render(name_file: 'clubs/index', params: $params, title: 'Clubs');
        };
    }

    #[Route('/clubs/form', 'clubs_form', ['GET', 'POST'])] public function indexForm(Request $request)
    {
        $auth_table = "clubs";
        $link_table = "clubs";
        $this_table = "clubs_form";
        $page_title = "Clubs";
        $page_localisation = "clubs/index_form";
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
                    $clubs = new ClubsModel();
                    $player = new PlayersModel();

                    if ($request->get->get('id')) {
                        $account = $clubs->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'player_id' => ['required'],
                                    'buildings_limit' => ['required'],
                                    'membership_fee' => ['required'],
                                    'price' => ['required'],
                                    'on_sale' => ['required'],
                                ]);

                                if (!$player->findById($request->post->get('player_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $clubs->setPlayerId($request->post->get('player_id'))
                                        ->setBuildingsLimit($request->post->get('buildings_limit'))
                                        ->setMembershipFee($request->post->get('membership_fee'))
                                        ->setPrice($request->post->get('price'))
                                        ->setOnSale($request->post->get('on_sale'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getPlayerId();
                            $data[] = $account->getBuildingsLimit();
                            $data[] = $account->getMembershipFee();
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
                                'membership_fee' => ['required'],
                                'price' => ['required'],
                                'on_sale' => ['required'],
                            ]);

                            if (!$player->findById($request->post->get('player_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $clubs->setPlayerId($request->post->get('player_id'))
                                    ->setBuildingsLimit($request->post->get('buildings_limit'))
                                    ->setMembershipFee($request->post->get('membership_fee'))
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

    #[Route('/club/buildings', 'club_buildings', ['GET', 'POST'])] public function clubBuildings(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("club_buildings", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("club_buildings", $tables)) {
                    $position = array_search("club_buildings", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $club_buildings = new Club_BuildingsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $clubid = $ids[0];
                            $buildingid = $ids[1];
                            $club_buildings->query("DELETE FROM {$club_buildings->getTableName()} WHERE club_id = $clubid AND building_id = $buildingid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'club/buildings');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($club_buildings->countLike($search_string, ["club_id", "building_id", "quantity"]));
                } else $nb_items = $club_buildings->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $club_buildings = $club_buildings->find($search_string, ["club_id", "building_id", "quantity"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($club_buildings as $club_building) {
                    $data[$i]['club_id'] = $club_building->getClubId();
                    $data[$i]['building_id'] = $club_building->getBuildingId();
                    $data[$i]['quantity'] = $club_building->getQuantity();
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

            $this->render(name_file: 'clubs/club_buildings', params: $params, title: 'Club buildings');
        };
    }

    #[Route('/club/buildings/form', 'club_buildings_form', ['GET', 'POST'])] public function clubBuildingsForm(Request $request)
    {
        $auth_table = "club_buildings";
        $link_table = "club_buildings";
        $this_table = "club_buildings_form";
        $page_title = "Club buildings";
        $page_localisation = "clubs/club_buildings_form";
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
                    $club_building = new Club_BuildingsModel();
                    $clubs = new ClubsModel();
                    $buildings = new BuildingsModel();

                    $club_id = $request->get->get('club_id');
                    $building_id = $request->get->get('building_id');

                    if ($club_id && $building_id) {
                        $account = $club_building->query("SELECT * FROM $auth_table WHERE club_id = '$club_id' AND building_id = '$building_id' LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'club_id' => ['required'],
                                    'building_id' => ['required'],
                                    'quantity' => ['required'],
                                ]);

                                if (!$clubs->findById($club_id) &&
                                    !$buildings->findById($building_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?club_id=".$club_id."&building_id=".$building_id);
                                } else {
                                    $setClubId = $request->post->get('club_id');
                                    $setBuildingId = $request->post->get('building_id');
                                    $setQuantity = $request->post->get('quantity');
                                    $club_building->query("UPDATE $auth_table SET club_id = '$setClubId', building_id = '$setBuildingId', quantity = '$setQuantity' WHERE club_id = '$club_id' AND building_id = '$building_id'");

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getClubId();
                            $data[] = $account->getBuildingId();
                            $data[] = $account->getQuantity();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'club_id' => ['required'],
                                'building_id' => ['required'],
                                'quantity' => ['required'],
                            ]);

                            if (!$clubs->findById($club_id) &&
                                !$buildings->findById($building_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $club_building->setClubId($request->post->get('club_id'))
                                    ->setBuildingId($request->post->get('building_id'))
                                    ->setQuantity($request->post->get('quantity'))
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

    #[Route('/club/items', 'club_items', ['GET', 'POST'])] public function clubItems(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("club_items", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("club_items", $tables)) {
                    $position = array_search("club_items", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $club_items = new Club_ItemsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $clubid = $ids[0];
                            $itemid = $ids[1];
                            $club_items->query("DELETE FROM {$club_items->getTableName()} WHERE club_id = $clubid AND item_id = $itemid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'club/items');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($club_items->countLike($search_string, ["club_id", "item_id", "quantity"]));
                } else $nb_items = $club_items->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $club_items = $club_items->find($search_string, ["club_id", "item_id", "quantity"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($club_items as $club_item) {
                    $data[$i]['club_id'] = $club_item->getClubId();
                    $data[$i]['item_id'] = $club_item->getItemId();
                    $data[$i]['quantity'] = $club_item->getQuantity();
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

            $this->render(name_file: 'clubs/club_items', params: $params, title: 'Club items');
        };
    }

    #[Route('/club/items/form', 'club_items_form', ['GET', 'POST'])] public function clubItemsForm(Request $request)
    {
        $auth_table = "club_items";
        $link_table = "club_items";
        $this_table = "club_items_form";
        $page_title = "Club items";
        $page_localisation = "clubs/club_items_form";
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
                    $club_item = new Club_ItemsModel();
                    $clubs = new ClubsModel();
                    $items = new ItemsModel();

                    $club_id = $request->get->get('club_id');
                    $item_id = $request->get->get('item_id');

                    if ($club_id && $item_id) {
                        $account = $club_item->query("SELECT * FROM $auth_table WHERE club_id = '$club_id' AND item_id = '$item_id' LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'club_id' => ['required'],
                                    'item_id' => ['required'],
                                    'quantity' => ['required'],
                                ]);

                                if (!$clubs->findById($club_id) &&
                                    !$items->findById($item_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?club_id=".$club_id."item_id".$item_id);
                                } else {
                                    $setClubId = $request->post->get('club_id');
                                    $setItemId = $request->post->get('item_id');
                                    $setQuantity = $request->post->get('quantity');
                                    $club_item->query("UPDATE $auth_table SET club_id = '$setClubId', item_id = '$setItemId', quantity = '$setQuantity' WHERE club_id = '$club_id' AND item_id = '$item_id'");


                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getClubId();
                            $data[] = $account->getItemId();
                            $data[] = $account->getQuantity();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'club_id' => ['required'],
                                'item_id' => ['required'],
                                'quantity' => ['required'],
                            ]);

                            if (!$clubs->findById($club_id) &&
                                !$items->findById($item_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $club_item->setClubId($request->post->get('club_id'))
                                    ->setItemId($request->post->get('item_id'))
                                    ->setQuantity($request->post->get('quantity'))
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

    #[Route('/club/members', 'club_members', ['GET', 'POST'])] public function clubMembers(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("club_members", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("club_members", $tables)) {
                    $position = array_search("club_members", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $club_members = new Club_MembersModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $clubid = $ids[0];
                            $playerid = $ids[1];
                            $club_members->query("DELETE FROM {$club_members->getTableName()} WHERE club_id = $clubid AND player_id = $playerid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'club/members');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($club_members->countLike($search_string, ["club_id", "player_id"]));
                } else $nb_items = $club_members->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $club_members = $club_members->find($search_string, ["club_id", "player_id"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($club_members as $club_item) {
                    $data[$i]['club_id'] = $club_item->getClubId();
                    $data[$i]['player_id'] = $club_item->getPlayerId();
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

            $this->render(name_file: 'clubs/club_members', params: $params, title: 'Club members');
        };
    }

    #[Route('/club/members/form', 'club_members_form', ['GET', 'POST'])] public function clubMembersForm(Request $request)
    {
        $auth_table = "club_members";
        $link_table = "club_members";
        $this_table = "club_members_form";
        $page_title = "Club members";
        $page_localisation = "clubs/club_members_form";
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
                    $club_member = new Club_MembersModel();
                    $clubs = new ClubsModel();
                    $players = new PlayersModel();

                    $club_id = $request->get->get('club_id');
                    $player_id = $request->get->get('player_id');

                    if ($club_id && $player_id) {
                        $account = $club_member->query("SELECT * FROM $auth_table WHERE club_id = '$club_id' AND player_id = '$player_id' LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'club_id' => ['required'],
                                    'player_id' => ['required'],
                                ]);

                                if (!$clubs->findById($club_id) &&
                                    !$players->findById($player_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?club_id=".$club_id."&player_id=".$player_id);
                                } else {
                                    $setClubId = $request->post->get('club_id');
                                    $setPlayerId = $request->post->get('player_id');
                                    $club_member->query("UPDATE $auth_table SET club_id = '$setClubId', player_id = '$setPlayerId' WHERE club_id = '$club_id' AND player_id = '$player_id'");

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getClubId();
                            $data[] = $account->getPlayerId();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'club_id' => ['required'],
                                'player_id' => ['required'],
                            ]);

                            if (!$clubs->findById($club_id) &&
                                !$players->findById($player_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $club_member->setClubId($request->post->get('club_id'))
                                    ->setPlayerId($request->post->get('player_id'))
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

    #[Route('/club/tournaments', 'club_tournaments', ['GET', 'POST'])] public function clubTournaments(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("club_tournaments", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("club_tournaments", $tables)) {
                    $position = array_search("club_tournaments", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $club_tournaments = new Club_TournamentsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $club_tournaments->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'clubs/tournaments');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($club_tournaments->countLike($search_string, ["id", "club_id", "name"]));
                } else $nb_items = $club_tournaments->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $club_tournaments = $club_tournaments->find($search_string, ["id", "club_id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($club_tournaments as $club_tournament) {
                    $data[$i]['id'] = $club_tournament->getId();
                    $data[$i]['club_id'] = $club_tournament->getClubId();
                    $data[$i]['name'] = $club_tournament->getName();
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

            $this->render(name_file: 'clubs/club_tournaments', params: $params, title: 'Club tournaments');
        }
    }

    #[Route('/club/tournaments/form', 'club_tournaments_form', ['GET', 'POST'])] public function clubTournamentsForm(Request $request)
    {
        $auth_table = "club_tournaments";
        $link_table = "club_tournaments";
        $this_table = "club_tournaments_form";
        $page_title = "Club tournaments";
        $page_localisation = "clubs/club_tournaments_form";
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
                    $club_tournament = new Club_TournamentsModel();
                    $clubs = new ClubsModel();

                    if ($request->get->get('id')) {
                        $account = $club_tournament->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'club_id' => ['required'],
                                    'name' => ['required'],
                                    'start_date' => ['required'],
                                    'end_date' => ['required'],
                                    'base_registration_fee' => ['required'],
                                    'member_registration_fee' => ['required'],
                                ]);

                                if (!$clubs->findById($request->post->get('club_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $club_tournament->setClubId($request->post->get('club_id'))
                                        ->setName($request->post->get('name'))
                                        ->setStartDate($request->post->get('start_date'))
                                        ->setEndDate($request->post->get('end_date'))
                                        ->setBaseRegistrationFee($request->post->get('base_registration_fee'))
                                        ->setMemberRegistrationFee($request->post->get('member_registration_fee'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getClubId();
                            $data[] = $account->getName();
                            $data[] = $account->getStartDate();
                            $data[] = $account->getEndDate();
                            $data[] = $account->getBaseRegistrationFee();
                            $data[] = $account->getMemberRegistrationFee();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'club_id' => ['required'],
                                'name' => ['required'],
                                'start_date' => ['required'],
                                'end_date' => ['required'],
                                'base_registration_fee' => ['required'],
                                'member_registration_fee' => ['required'],
                            ]);

                            if (!$clubs->findById($request->post->get('club_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $club_tournament->setClubId($request->post->get('club_id'))
                                    ->setName($request->post->get('name'))
                                    ->setStartDate($request->post->get('start_date'))
                                    ->setEndDate($request->post->get('end_date'))
                                    ->setBaseRegistrationFee($request->post->get('base_registration_fee'))
                                    ->setMemberRegistrationFee($request->post->get('member_registration_fee'))
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

    #[Route('/club/tournament/registrations', 'club_tournament_registrations', ['GET', 'POST'])] public function clubTournamentRegistrations(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("club_tournament_registrants", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("club_tournament_registrants", $tables)) {
                    $position = array_search("club_tournament_registrants", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $club_tournament_registrations = new Club_Tournament_RegistrantsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $clubtournamentid = $ids[0];
                            $playerid = $ids[1];
                            $club_tournament_registrations->query("DELETE FROM {$club_tournament_registrations->getTableName()} WHERE club_tournament_id = $clubtournamentid AND player_id = $playerid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'club/tournament/registrations');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($club_tournament_registrations->countLike($search_string, ["club_tournament_id", "player_id", "rank"]));
                } else $nb_items = $club_tournament_registrations->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $club_tournament_registrations = $club_tournament_registrations->find($search_string, ["club_tournament_id", "player_id", "rank"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($club_tournament_registrations as $club_tournament_registration) {
                    $data[$i]['club_tournament_id'] = $club_tournament_registration->getClubTournamentId();
                    $data[$i]['player_id'] = $club_tournament_registration->getPlayerId();
                    $data[$i]['rank'] = $club_tournament_registration->getRank();
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

            $this->render(name_file: 'clubs/club_tournament_registrations', params: $params, title: 'Club tournament registrations');
        }
    }

    #[Route('/club/tournament/registrations/form', 'club_tournament_registrations_form', ['GET', 'POST'])] public function clubTournamentRegistrationsForm(Request $request)
    {
        $auth_table = "club_tournament_registrants";
        $link_table = "club_tournament_registrations";
        $this_table = "club_tournament_registrations_form";
        $page_title = "Club tournament registrations";
        $page_localisation = "clubs/club_tournament_registrations_form";
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
                    $club_tournament_registration = new Club_Tournament_RegistrantsModel();
                    $club_tournament = new Club_TournamentsModel();
                    $players = new PlayersModel();

                    $club_tournament_id = $request->get->get('club_tournament_id');
                    $player_id = $request->get->get('player_id');

                    if ($club_tournament_id && $player_id) {
                        $account = $club_tournament_registration->query("SELECT * FROM $auth_table WHERE club_tournament_id = '$club_tournament_id' AND player_id = '$player_id' LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'club_tournament_id' => ['required'],
                                    'player_id' => ['required'],
                                    'rank' => ['required'],
                                ]);

                                if (!$club_tournament->findById($club_tournament_id) &&
                                    !$players->findById($player_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?club_tournament_id=".$club_tournament_id."&player_id=".$player_id);
                                } else {
                                    $setClubTournamentId = $request->post->get('club_tournament_id');
                                    $setPlayerId = $request->post->get('player_id');
                                    $setRank = $request->post->get('rank');
                                    $club_tournament_registration->query("UPDATE $auth_table SET club_tournament_id = '$setClubTournamentId', player_id = '$setPlayerId', rank = '$setRank' WHERE club_tournament_id = '$club_tournament_id' AND player_id = '$player_id'");

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getClubTournamentId();
                            $data[] = $account->getPlayerId();
                            $data[] = $account->getRank();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'club_tournament_id' => ['required'],
                                'player_id' => ['required'],
                                'rank' => ['required'],
                            ]);

                            if (!$club_tournament->findById($club_tournament_id) &&
                                !$players->findById($player_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $club_tournament_registration->setClubTournamentId($request->post->get('club_tournament_id'))
                                    ->setPlayerId($request->post->get('player_id'))
                                    ->setRank($request->post->get('rank'))
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

    #[Route('/club/tournament/rewards', 'club_tournament_rewards', ['GET', 'POST'])] public function clubTournamentRewards(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("club_tournament_rewards", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("club_tournament_rewards", $tables)) {
                    $position = array_search("club_tournament_rewards", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $club_tournament_rewards = new Club_Tournament_RewardsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $club_tournament_rewards->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'club/tournament/rewards');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($club_tournament_rewards->countLike($search_string, ["id", "club_tournament_id", "item_id", "quantity", "obtention_rank"]));
                } else $nb_items = $club_tournament_rewards->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $club_tournament_rewards = $club_tournament_rewards->find($search_string, ["id", "club_tournament_id", "item_id", "quantity", "obtention_rank"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($club_tournament_rewards as $club_tournament_reward) {
                    $data[$i]['id'] = $club_tournament_reward->getId();
                    $data[$i]['club_tournament_id'] = $club_tournament_reward->getClubTournamentId();
                    $data[$i]['item_id'] = $club_tournament_reward->getItemId();
                    $data[$i]['quantity'] = $club_tournament_reward->getQuantity();
                    $data[$i]['obtention_rank'] = $club_tournament_reward->getObtentionRank();
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

            $this->render(name_file: 'clubs/club_tournament_rewards', params: $params, title: 'Club tournament rewards');
        }
    }

    #[Route('/club/tournament/rewards/form', 'club_tournament_rewards_form', ['GET', 'POST'])] public function clubTournamentRewardsForm(Request $request)
    {
        $auth_table = "club_tournament_registrants";
        $link_table = "club_tournament_rewards";
        $this_table = "club_tournament_rewards_form";
        $page_title = "Club tournament rewards";
        $page_localisation = "clubs/club_tournament_rewards_form";
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
                    $club_tournament_rewards = new Club_Tournament_RewardsModel();
                    $club_tournament = new Club_TournamentsModel();
                    $items = new ItemsModel();

                    if ($request->get->get('id')) {
                        $account = $club_tournament_rewards->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'club_tournament_id' => ['required'],
                                    'item_id' => ['required'],
                                    'quantity' => ['required'],
                                    'obtention_rank' => ['required'],
                                ]);

                                if (!$club_tournament->findById($request->post->get('club_tournament_id')) &&
                                    !$items->findById($request->post->get('item_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $club_tournament_rewards->setClubTournamentId($request->post->get('club_tournament_id'))
                                        ->setItemId($request->post->get('item_id'))
                                        ->setQuantity($request->post->get('quantity'))
                                        ->setObtentionRank($request->post->get('obtention_rank'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getClubTournamentId();
                            $data[] = $account->getItemId();
                            $data[] = $account->getQuantity();
                            $data[] = $account->getObtentionRank();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'club_tournament_id' => ['required'],
                                'item_id' => ['required'],
                                'quantity' => ['required'],
                                'obtention_rank' => ['required'],
                            ]);

                            if (!$club_tournament->findById($request->post->get('club_tournament_id')) &&
                                !$items->findById($request->post->get('item_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $club_tournament_rewards->setClubTournamentId($request->post->get('club_tournament_id'))
                                    ->setItemId($request->post->get('item_id'))
                                    ->setQuantity($request->post->get('quantity'))
                                    ->setObtentionRank($request->post->get('obtention_rank'))
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
