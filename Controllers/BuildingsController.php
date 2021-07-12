<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\Classes\Validator;
use App\Core\System\Controller;
use App\Models\Automatic_Task_ActionsModel;
use App\Models\Automatic_TasksModel;
use App\Models\Building_FamiliesModel;
use App\Models\Building_ItemsModel;
use App\Models\Building_TypesModel;
use App\Models\BuildingsModel;
use App\Models\ItemsModel;
use App\Models\Stable_BuildingsModel;

final class BuildingsController extends Controller {

    #[Route('/buildings', 'buildings', ['GET', 'POST'])] public function index(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("buildings", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("buildings", $tables)) {
                    $position = array_search("buildings", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $buildings = new BuildingsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $buildings->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'buildings');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($buildings->countLike($search_string, ["id", "building_type_id", "description", "level"]));
                } else $nb_items = $buildings->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $buildings = $buildings->find($search_string, ["id", "building_type_id", "description", "level"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($buildings as $building) {
                    $data[$i]['id'] = $building->getId();
                    $data[$i]['building_type_id'] = $building->getBuildingTypeId();
                    $data[$i]['description'] = $building->getDescription();
                    $data[$i]['level'] = $building->getLevel();
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

            $this->render(name_file: 'buildings/index', params: $params, title: 'Buildings');
        }
    }

    #[Route('/buildings/form', 'buildings_form', ['GET', 'POST'])] public function indexForm(Request $request)
    {
        $auth_table = "buildings";
        $link_table = "buildings";
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
                    $buildings = new BuildingsModel();
                    $building_type = new Building_TypesModel();
                    $building_family = new Building_FamiliesModel();

                    if ($request->get->get('id')) {
                        $account = $buildings->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse('buildings'));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'building_type_id' => ['required'],
                                    'building_family_id' => ['required'],
                                    'description' => ['required'],
                                    'level' => ['required'],
                                    'price' => ['required'],
                                    'on_sale' => ['required'],
                                ]);

                                if (!$building_type->findById($request->post->get('building_type_id')) && !$building_family->findById($request->post->get('building_family_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect("/buildings/form?id=".$request->get->get('id'));
                                } else {
                                    $buildings->setBuildingTypeId($request->post->get('building_type_id'))
                                        ->setBuildingFamilyId($request->post->get('building_family_id'))
                                        ->setDescription($request->post->get('description'))
                                        ->setLevel($request->post->get('level'))
                                        ->setPrice($request->post->get('price'))
                                        ->setOnSale($request->post->get('on_sale'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse('buildings'));
                                }
                            }

                            $data[] = $account->getBuildingTypeId();
                            $data[] = $account->getBuildingFamilyId();
                            $data[] = $account->getDescription();
                            $data[] = $account->getLevel();
                            $data[] = $account->getPrice();
                            $data[] = $account->getOnSale();

                            $this->render(name_file: 'buildings/index_form', params: [
                                "data"=> $data,
                            ], title: 'Buildings');
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'building_type_id' => ['required'],
                                'building_family_id' => ['required'],
                                'description' => ['required'],
                                'level' => ['required'],
                                'price' => ['required'],
                                'on_sale' => ['required'],
                            ]);

                            if (!$building_type->findById($request->post->get('building_type_id')) && !$building_family->findById($request->post->get('building_family_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse('buildings_form'));
                            } else {
                                $buildings->setBuildingTypeId($request->post->get('building_type_id'))
                                    ->setBuildingFamilyId($request->post->get('building_family_id'))
                                    ->setDescription($request->post->get('description'))
                                    ->setLevel($request->post->get('level'))
                                    ->setPrice($request->post->get('price'))
                                    ->setOnSale($request->post->get('on_sale'))
                                    ->create();

                                $this->addFlash('success', "Les données ont été ajouté dans la table.");
                                $this->redirect(self::reverse('buildings'));
                            }
                        }

                        $this->render(name_file: 'buildings/index_form', title: 'Buildings');
                    }
                }
            }
        }
    }

    #[Route('/building/families', 'building_families', ['GET', 'POST'])] public function buildingFamilies(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("building_families", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("building_families", $tables)) {
                    $position = array_search("building_families", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $building_families = new Building_FamiliesModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $building_families->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'building/families');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($building_families->countLike($search_string, ["id", "name"]));
                } else $nb_items = $building_families->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $building_families = $building_families->find($search_string, ["id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($building_families as $building_family) {
                    $data[$i]['id'] = $building_family->getId();
                    $data[$i]['name'] = $building_family->getName();
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

            $this->render(name_file: 'buildings/building_families', params: $params, title: 'Building families');
        }
    }

    #[Route('/building/families/form', 'building_families_form', ['GET', 'POST'])] public function buildingFamiliesForm(Request $request)
    {
        $auth_table = "building_families";
        $link_table = "building_families";
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
                    $building_family = new Building_FamiliesModel();

                    if ($request->get->get('id')) {
                        $account = $building_family->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse('building_families'));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                ]);

                                $building_family->setName($request->post->get('name'))
                                    ->update($request->get->get('id'));

                                $this->addFlash('success', "Les données ont été modifiées.");
                                $this->redirect(self::reverse('building_families'));
                            }

                            $data[] = $account->getName();

                            $this->render(name_file: 'buildings/building_families_form', params: [
                                "data"=> $data,
                            ], title: 'Building families');
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'name' => ['required'],
                            ]);

                            $building_family->setName($request->post->get('name'))
                                ->create();

                            $this->addFlash('success', "Les données ont été ajouté dans la table.");
                            $this->redirect(self::reverse('building_families'));
                        }

                        $this->render(name_file: 'buildings/building_families_form', title: 'Building families');
                    }
                }
            }
        }
    }

    #[Route('/building/items', 'building_items', ['GET', 'POST'])] public function buildingItems(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("building_items", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("building_items", $tables)) {
                    $position = array_search("building_items", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $building_items = new Building_ItemsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $buildingid = $ids[0];
                            $itemid = $ids[1];
                            $building_items->query("DELETE FROM {$building_items->get()} WHERE building_id = $buildingid AND item_id = $itemid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'building/items');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($building_items->countLike($search_string, ["building_id", "item_id", "quantity"]));
                } else $nb_items = $building_items->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $building_items = $building_items->find($search_string, ["building_id", "item_id", "quantity"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($building_items as $row) {
                    $data[$i]['building_id'] = $row->getBuildingId();
                    $data[$i]['item_id'] = $row->getItemId();
                    $data[$i]['quantity'] = $row->getQuantity();
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

            $this->render(name_file: 'buildings/building_items', params: $params, title: 'Building items');
        }
    }

    #[Route('/building/items/form', 'building_items_form', ['GET', 'POST'])] public function buildingItemsForm(Request $request)
    {
        $auth_table = "building_items";
        $link_table = "building_items";
        $this_table = "building_items_form";
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
                    $buildings = new BuildingsModel();
                    $items = new ItemsModel();
                    $building_item = new Building_ItemsModel();

                    $building_id = $request->get->get('building_id');
                    $item_id = $request->get->get('item_id');

                    if ($building_id && $item_id) {
                        $account = $building_item->query("SELECT * FROM $auth_table WHERE building_id = '$building_id' AND item_id = '$item_id' LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'building_id' => ['required'],
                                    'item_id' => ['required'],
                                    'quantity' => ['required'],
                                ]);

                                if (!$buildings->findById($building_id) &&
                                    !$items->findById($item_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?building_id=".$building_id."&item_id=".$item_id);
                                } else {
                                    $setBuildingId = $request->post->get('building_id');
                                    $setItemId = $request->post->get('item_id');
                                    $setQuantity = $request->post->get('quantity');
                                    $building_item->query("UPDATE $auth_table SET building_id = '$setBuildingId', item_id = '$setItemId', quantity = '$setQuantity' WHERE building_id = '$building_id' AND item_id = '$item_id'");

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getBuildingId();
                            $data[] = $account->getItemId();
                            $data[] = $account->getQuantity();

                            $this->render(name_file: 'buildings/building_items_form', params: [
                                "data"=> $data,
                            ], title: 'Building items');
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'building_id' => ['required'],
                                'item_id' => ['required'],
                                'quantity' => ['required'],
                            ]);

                            if (!$buildings->findById($building_id) &&
                                !$items->findById($item_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $building_item->setBuildingId($request->post->get('building_id'))
                                    ->setItemId($request->post->get('item_id'))
                                    ->setQuantity($request->post->get('quantity'))
                                    ->create();

                                $this->addFlash('success', "Les données ont été ajouté dans la table.");
                                $this->redirect(self::reverse($link_table));
                            }
                        }

                        $this->render(name_file: 'buildings/building_items_form', title: 'Building items');
                    }
                }
            }
        }
    }

    #[Route('/building/types', 'building_types', ['GET', 'POST'])] public function buildingTypes(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("building_types", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("building_types", $tables)) {
                    $position = array_search("building_types", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $building_types = new Building_TypesModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $building_types->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'building/types');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($building_types->countLike($search_string, ["id", "name", "items_limit", "horses_limit"]));
                } else $nb_items = $building_types->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $building_types = $building_types->find($search_string, ["id", "name", "items_limit", "horses_limit"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($building_types as $building_type) {
                    $data[$i]['id'] = $building_type->getId();
                    $data[$i]['name'] = $building_type->getName();
                    $data[$i]['items_limit'] = $building_type->getItemsLimit();
                    $data[$i]['horses_limit'] = $building_type->getHorsesLimit();
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

            $this->render(name_file: 'buildings/building_types', params: $params, title: 'Building types');
        }
    }

    #[Route('/building/types/form', 'building_types_form', ['GET', 'POST'])] public function buildingTypesForm(Request $request)
    {
        $auth_table = "building_types";
        $link_table = "building_types";
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
                    $building_type = new Building_TypesModel();

                    if ($request->get->get('id')) {
                        $account = $building_type->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                    'items_limit' => ['required'],
                                    'horses_limit' => ['required'],
                                ]);

                                $building_type->setName($request->post->get('name'))
                                    ->setItemsLimit($request->post->get('items_limit'))
                                    ->setHorsesLimit($request->post->get('horses_limit'))
                                    ->update($request->get->get('id'));

                                $this->addFlash('success', "Les données ont été modifiées.");
                                $this->redirect(self::reverse($link_table));
                            }

                            $data[] = $account->getName();
                            $data[] = $account->getItemsLimit();
                            $data[] = $account->getHorsesLimit();

                            $this->render(name_file: 'buildings/building_types_form', params: [
                                "data"=> $data,
                            ], title: 'Building types');
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'name' => ['required'],
                                'items_limit' => ['required'],
                                'horses_limit' => ['required'],
                            ]);

                            $building_type->setName($request->post->get('name'))
                                ->setItemsLimit($request->post->get('items_limit'))
                                ->setHorsesLimit($request->post->get('horses_limit'))
                                ->create();

                            $this->addFlash('success', "Les données ont été ajouté dans la table.");
                            $this->redirect(self::reverse($link_table));
                        }

                        $this->render(name_file: 'buildings/building_types_form', title: 'Building types');
                    }
                }
            }
        }
    }

    #[Route('/automatic', 'automatic_tasks', ['GET', 'POST'])] public function automaticTasks(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("automatic_tasks", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("automatic_tasks", $tables)) {
                    $position = array_search("automatic_tasks", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $automatic_tasks = new Automatic_TasksModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $automatic_tasks->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'automatic');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($automatic_tasks->countLike($search_string, ["id", "automatic_task_action_id", "stable_building_id", "item_id", "frequency"]));
                } else $nb_items = $automatic_tasks->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $automatic_tasks = $automatic_tasks->find($search_string, ["id", "automatic_task_action_id", "stable_building_id", "item_id", "frequency"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($automatic_tasks as $automatic_task) {
                    $data[$i]['id'] = $automatic_task->getId();
                    $data[$i]['automatic_task_action_id'] = $automatic_task->getAutomaticTaskActionId();
                    $data[$i]['stable_building_id'] = $automatic_task->getStableBuildingId();
                    $data[$i]['item_id'] = $automatic_task->getItemId();
                    $data[$i]['frequency'] = $automatic_task->getFrequency();
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

            $this->render(name_file: 'buildings/automatic_tasks', params: $params, title: 'Building automatic tasks');
        }
    }

    #[Route('/automatic/form', 'automatic_tasks_form', ['GET', 'POST'])] public function automaticTasksForm(Request $request)
    {
        $auth_table = "automatic_tasks";
        $link_table = "automatic_tasks";
        $this_table = "automatic_tasks_form";
        $page_title = "Building automatic tasks";
        $page_localisation = "buildings/automatic_tasks_form";
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
                    $automatic_tasks = new Automatic_TasksModel();
                    $automatic_tasks_action = new Automatic_Task_ActionsModel();
                    $stable_building = new Stable_BuildingsModel();
                    $items = new ItemsModel();

                    if ($request->get->get('id')) {
                        $account = $automatic_tasks->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'automatic_task_action_id' => ['required'],
                                    'stable_building_id' => ['required'],
                                    'item_id' => ['required'],
                                    'frequency' => ['required'],
                                ]);

                                if (!$automatic_tasks_action->findById($request->post->get('automatic_task_action_id')) &&
                                    !$stable_building->findById($request->post->get('stable_building_id')) &&
                                    !$items->findById($request->post->get('item_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $automatic_tasks->setAutomaticTaskActionId($request->post->get('automatic_task_action_id'))
                                        ->setStableBuildingId($request->post->get('stable_building_id'))
                                        ->setItemId($request->post->get('item_id'))
                                        ->setFrequency($request->post->get('frequency'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getAutomaticTaskActionId();
                            $data[] = $account->getStableBuildingId();
                            $data[] = $account->getItemId();
                            $data[] = $account->getFrequency();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'automatic_task_action_id' => ['required'],
                                'stable_building_id' => ['required'],
                                'item_id' => ['required'],
                                'frequency' => ['required'],
                            ]);

                            if (!$automatic_tasks_action->findById($request->post->get('automatic_task_action_id')) &&
                                !$stable_building->findById($request->post->get('stable_building_id')) &&
                                !$items->findById($request->post->get('item_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $automatic_tasks->setAutomaticTaskActionId($request->post->get('automatic_task_action_id'))
                                    ->setStableBuildingId($request->post->get('stable_building_id'))
                                    ->setItemId($request->post->get('item_id'))
                                    ->setFrequency($request->post->get('frequency'))
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

    #[Route('/automatic/actions', 'automatic_task_actions', ['GET', 'POST'])] public function automaticTaskAction(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("automatic_task_actions", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("automatic_task_actions", $tables)) {
                    $position = array_search("automatic_task_actions", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $automatic_task_actions = new Automatic_Task_ActionsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $automatic_task_actions->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'automatic/actions');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($automatic_task_actions->countLike($search_string, ["id", "name"]));
                } else $nb_items = $automatic_task_actions->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $automatic_task_actions = $automatic_task_actions->find($search_string, ["id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($automatic_task_actions as $automatic_task_action) {
                    $data[$i]['id'] = $automatic_task_action->getId();
                    $data[$i]['name'] = $automatic_task_action->getName();
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

            $this->render(name_file: 'buildings/automatic_task_actions', params: $params, title: 'Building automatic task actions');
        }
    }

    #[Route('/automatic/actions/form', 'automatic_task_actions_form', ['GET', 'POST'])] public function automaticTaskActionForm(Request $request)
    {
        $auth_table = "automatic_task_actions";
        $link_table = "automatic_task_actions";
        $page_title = "Building automatic task actions";
        $page_localisation = "buildings/automatic_task_actions_form";
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
                    $automatic_tasks_action = new Automatic_Task_ActionsModel();

                    if ($request->get->get('id')) {
                        $account = $automatic_tasks_action->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                ]);

                                $automatic_tasks_action->setName($request->post->get('name'))
                                    ->update($request->get->get('id'));

                                $this->addFlash('success', "Les données ont été modifiées.");
                                $this->redirect(self::reverse($link_table));
                            }

                            $data[] = $account->getName();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'name' => ['required'],
                            ]);

                            $automatic_tasks_action->setName($request->post->get('name'))->create();

                            $this->addFlash('success', "Les données ont été ajouté dans la table.");
                            $this->redirect(self::reverse($link_table));
                        }

                        $this->render(name_file: $page_localisation, title: $page_title);
                    }
                }
            }
        }
    }
}
