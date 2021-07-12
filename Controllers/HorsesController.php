<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\Classes\Validator;
use App\Core\System\Controller;
use App\Models\Horse_BreedsModel;
use App\Models\Horse_ItemsModel;
use App\Models\Horse_StatusModel;
use App\Models\HorsesModel;
use App\Models\ItemsModel;
use App\Models\StatusesModel;

final class HorsesController extends Controller {

    #[Route('/horses', 'horses', ['GET', 'POST'])] public function index(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("horses", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("horses", $tables)) {
                    $position = array_search("horses", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $horses = new HorsesModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $horses->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'horses');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($horses->countLike($search_string, ["id", "name", "breed_id", "global_condition", "experience", "level"]));
                } else $nb_items = $horses->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $horses = $horses->find($search_string, ["id", "name", "breed_id", "global_condition", "experience", "level"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($horses as $horse) {
                    $data[$i]['id'] = $horse->getId();
                    $data[$i]['name'] = $horse->getName();
                    $data[$i]['breed_id'] = $horse->getBreedId();
                    $data[$i]['global_condition'] = $horse->getGlobalCondition();
                    $data[$i]['experience'] = $horse->getExperience();
                    $data[$i]['level'] = $horse->getLevel();
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

            $this->render(name_file: 'horses/index', params: $params, title: 'Horses');
        };
    }

    #[Route('/horses/form', 'horses_form', ['GET', 'POST'])] public function indexForm(Request $request)
    {
        $auth_table = "horses";
        $link_table = "horses";
        $this_table = "horses_form";
        $page_title = "Horses";
        $page_localisation = "horses/index_form";
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
                    $horses = new HorsesModel();
                    $horse_breeds = new Horse_BreedsModel();

                    if ($request->get->get('id')) {
                        $account = $horses->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                    'breed_id' => ['required'],
                                    'health' => ['required'],
                                    'fatigue' => ['required'],
                                    'morale' => ['required'],
                                    'stress' => ['required'],
                                    'hunger' => ['required'],
                                    'cleanliness' => ['required'],
                                    'global_condition' => ['required'],
                                    'resistance' => ['required'],
                                    'stamina' => ['required'],
                                    'jump' => ['required'],
                                    'speed' => ['required'],
                                    'sociability' => ['required'],
                                    'intelligence' => ['required'],
                                    'temperament' => ['required'],
                                    'experience' => ['required'],
                                    'level' => ['required'],
                                ]);

                                if (!$horse_breeds->findById($request->post->get('breed_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $horses->setName($request->post->get('name'))
                                        ->setBreedId($request->post->get('breed_id'))
                                        ->setHealth($request->post->get('health'))
                                        ->setFatigue($request->post->get('fatigue'))
                                        ->setMorale($request->post->get('morale'))
                                        ->setStress($request->post->get('stress'))
                                        ->setHunger($request->post->get('hunger'))
                                        ->setCleanliness($request->post->get('cleanliness'))
                                        ->setGlobalCondition($request->post->get('global_condition'))
                                        ->setResistance($request->post->get('resistance'))
                                        ->setStamina($request->post->get('stamina'))
                                        ->setJump($request->post->get('jump'))
                                        ->setSpeed($request->post->get('speed'))
                                        ->setSociability($request->post->get('sociability'))
                                        ->setIntelligence($request->post->get('intelligence'))
                                        ->setTemperament($request->post->get('temperament'))
                                        ->setExperience($request->post->get('experience'))
                                        ->setLevel($request->post->get('level'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getName();
                            $data[] = $account->getBreedId();
                            $data[] = $account->getHealth();
                            $data[] = $account->getFatigue();
                            $data[] = $account->getMorale();
                            $data[] = $account->getStress();
                            $data[] = $account->getHunger();
                            $data[] = $account->getCleanliness();
                            $data[] = $account->getGlobalCondition();
                            $data[] = $account->getResistance();
                            $data[] = $account->getStamina();
                            $data[] = $account->getJump();
                            $data[] = $account->getSpeed();
                            $data[] = $account->getSociability();
                            $data[] = $account->getIntelligence();
                            $data[] = $account->getTemperament();
                            $data[] = $account->getExperience();
                            $data[] = $account->getLevel();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'name' => ['required'],
                                'breed_id' => ['required'],
                                'health' => ['required'],
                                'fatigue' => ['required'],
                                'morale' => ['required'],
                                'stress' => ['required'],
                                'hunger' => ['required'],
                                'cleanliness' => ['required'],
                                'global_condition' => ['required'],
                                'resistance' => ['required'],
                                'stamina' => ['required'],
                                'jump' => ['required'],
                                'speed' => ['required'],
                                'sociability' => ['required'],
                                'intelligence' => ['required'],
                                'temperament' => ['required'],
                                'experience' => ['required'],
                                'level' => ['required'],
                            ]);

                            if (!$horse_breeds->findById($request->post->get('breed_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $horses->setName($request->post->get('name'))
                                    ->setBreedId($request->post->get('breed_id'))
                                    ->setHealth($request->post->get('health'))
                                    ->setFatigue($request->post->get('fatigue'))
                                    ->setMorale($request->post->get('morale'))
                                    ->setStress($request->post->get('stress'))
                                    ->setHunger($request->post->get('hunger'))
                                    ->setCleanliness($request->post->get('cleanliness'))
                                    ->setGlobalCondition($request->post->get('global_condition'))
                                    ->setResistance($request->post->get('resistance'))
                                    ->setStamina($request->post->get('stamina'))
                                    ->setJump($request->post->get('jump'))
                                    ->setSpeed($request->post->get('speed'))
                                    ->setSociability($request->post->get('sociability'))
                                    ->setIntelligence($request->post->get('intelligence'))
                                    ->setTemperament($request->post->get('temperament'))
                                    ->setExperience($request->post->get('experience'))
                                    ->setLevel($request->post->get('level'))
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

    #[Route('/horse/breeds', 'horse_breeds', ['GET', 'POST'])] public function horseBreeds(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("horse_breeds", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("horse_breeds", $tables)) {
                    $position = array_search("horse_breeds", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $horse_breeds = new Horse_BreedsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $horse_breeds->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'horse/breeds');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($horse_breeds->countLike($search_string, ["id", "name"]));
                } else $nb_items = $horse_breeds->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $horse_breeds = $horse_breeds->find($search_string, ["id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($horse_breeds as $horse_breed) {
                    $data[$i]['id'] = $horse_breed->getId();
                    $data[$i]['name'] = $horse_breed->getName();
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

            $this->render(name_file: 'horses/horse_breeds', params: $params, title: 'Horse breeds');
        };
    }

    #[Route('/horse/breeds/form', 'horse_breeds_form', ['GET', 'POST'])] public function horseBreedsForm(Request $request)
    {
        $auth_table = "horse_breeds";
        $link_table = "horse_breeds";
        $page_title = "Horse breeds";
        $page_localisation = "horses/horse_breeds_form";
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
                    $horse_breeds = new Horse_BreedsModel();

                    if ($request->get->get('id')) {
                        $account = $horse_breeds->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                    'description' => ['required'],
                                ]);

                                $horse_breeds->setName($request->post->get('name'))
                                    ->setDescription($request->post->get('description'))
                                    ->update($request->get->get('id'));

                                $this->addFlash('success', "Les données ont été modifiées.");
                                $this->redirect(self::reverse($link_table));
                            }

                            $data[] = $account->getName();
                            $data[] = $account->getDescription();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'name' => ['required'],
                                'description' => ['required'],
                            ]);

                            $horse_breeds->setName($request->post->get('name'))
                                ->setDescription($request->post->get('description'))
                                ->create();

                            $this->addFlash('success', "Les données ont été ajouté dans la table.");
                            $this->redirect(self::reverse($link_table));
                        }

                        $this->render(name_file: $page_localisation, title: $page_title);
                    }
                }
            }
        }
    }

    #[Route('/horse/items', 'horse_items', ['GET', 'POST'])] public function horseItems(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("horse_items", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("horse_items", $tables)) {
                    $position = array_search("horse_items", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $horse_items = new Horse_ItemsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $horseid = $ids[0];
                            $itemid = $ids[1];
                            $horse_items->query("DELETE FROM {$horse_items->getTableName()} WHERE horse_id = $horseid AND item_id = $itemid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'horse/items');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($horse_items->countLike($search_string, ["horse_id", "item_id", "quantity"]));
                } else $nb_items = $horse_items->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $horse_items = $horse_items->find($search_string, ["horse_id", "item_id", "quantity"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($horse_items as $horse_item) {
                    $data[$i]['horse_id'] = $horse_item->getHorseId();
                    $data[$i]['item_id'] = $horse_item->getItemId();
                    $data[$i]['quantity'] = $horse_item->getQuantity();
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

            $this->render(name_file: 'horses/horse_items', params: $params, title: 'Horse items');
        };
    }

    #[Route('/horse/items/form', 'horse_items_form', ['GET', 'POST'])] public function horseItemsForm(Request $request)
    {
        $auth_table = "horse_items";
        $link_table = "horse_items";
        $this_table = "horse_items_form";
        $page_title = "Horse items";
        $page_localisation = "horses/horse_items_form";
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
                    $horse_items = new Horse_ItemsModel();
                    $horses = new HorsesModel();
                    $items = new ItemsModel();

                    $horse_id = $request->get->get('horse_id');
                    $item_id = $request->get->get('item_id');

                    if ($horse_id && $item_id) {
                        $account = $horse_items->query("SELECT * FROM $auth_table WHERE horse_id = '$horse_id' AND item_id = '$item_id' LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'horse_id' => ['required'],
                                    'item_id' => ['required'],
                                    'quantity' => ['required'],
                                ]);

                                if (!$horses->findById($horse_id) &&
                                    !$items->findById($item_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?horse_id=".$horse_id."&item_id=".$item_id);
                                } else {
                                    $setHorseId = $request->post->get('horse_id');
                                    $setItemId = $request->post->get('item_id');
                                    $setQuantity = $request->post->get('quantity');
                                    $horse_items->query("UPDATE $auth_table SET horse_id = '$setHorseId', item_id = '$setItemId', quantity = '$setQuantity' WHERE horse_id = '$horse_id' AND item_id = '$item_id'");

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getHorseId();
                            $data[] = $account->getItemId();
                            $data[] = $account->getQuantity();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'horse_id' => ['required'],
                                'item_id' => ['required'],
                                'quantity' => ['required'],
                            ]);

                            if (!$horses->findById($horse_id) &&
                                !$items->findById($item_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $horse_items->setHorseId($request->post->get('horse_id'))
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

    #[Route('/horse/status', 'horse_status', ['GET', 'POST'])] public function horseStatus(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("horse_status", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("horse_status", $tables)) {
                    $position = array_search("horse_status", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $horse_status = new Horse_StatusModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $horseid = $ids[0];
                            $statusid = $ids[1];
                            $horse_status->query("DELETE FROM {$horse_status->getTableName()} WHERE horse_id = $horseid AND status_id = $statusid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'horse/status');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($horse_status->countLike($search_string, ["horse_id", "status_id", "onset_date"]));
                } else $nb_items = $horse_status->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $horse_status = $horse_status->find($search_string, ["horse_id", "status_id", "onset_date"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($horse_status as $row) {
                    $data[$i]['horse_id'] = $row->getHorseId();
                    $data[$i]['status_id'] = $row->getStatusId();
                    $data[$i]['onset_date'] = $row->getOnsetDate();
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

            $this->render(name_file: 'horses/horse_status', params: $params, title: 'Horse status');
        }
    }

    #[Route('/horse/status/form', 'horse_status_form', ['GET', 'POST'])] public function horseStatusForm(Request $request)
    {
        $auth_table = "horse_status";
        $link_table = "horse_status";
        $this_table = "horse_status_form";
        $page_title = "Horse status";
        $page_localisation = "horses/horse_status_form";
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
                    $horse_status = new Horse_StatusModel();
                    $horses = new HorsesModel();
                    $status = new StatusesModel();

                    $horse_id = $request->get->get('horse_id');
                    $status_id = $request->get->get('status_id');

                    if ($horse_id && $status_id) {
                        $account = $horse_status->query("SELECT * FROM $auth_table WHERE horse_id = '$horse_id' AND status_id = '$status_id' LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'horse_id' => ['required'],
                                    'status_id' => ['required'],
                                    'onset_date' => ['required'],
                                ]);

                                if (!$horses->findById($horse_id) &&
                                    !$status->findById($status_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?horse_id=".$horse_id."&status_id=".$status_id);
                                } else {
                                    $setHorseId = $request->post->get('horse_id');
                                    $setStatusId = $request->post->get('status_id');
                                    $setOnsetDate = $request->post->get('onset_date');
                                    $horse_status->query("UPDATE $auth_table SET horse_id = '$setHorseId', status_id = '$setStatusId', onset_date = '$setOnsetDate' WHERE horse_id = '$horse_id' AND status_id = '$status_id'");

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getHorseId();
                            $data[] = $account->getStatusId();
                            $data[] = $account->getOnsetDate();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'horse_id' => ['required'],
                                'status_id' => ['required'],
                                'onset_date' => ['required'],
                            ]);

                            if (!$horses->findById($horse_id) &&
                                !$status->findById($status_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $horse_status->setHorseId($request->post->get('horse_id'))
                                    ->setStatusId($request->post->get('status_id'))
                                    ->setOnsetDate($request->post->get('onset_date'))
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

    #[Route('/statuses', 'statuses', ['GET', 'POST'])] public function statuses(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("statuses", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("statuses", $tables)) {
                    $position = array_search("statuses", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $statuses = new StatusesModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $statuses->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'statuses');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($statuses->countLike($search_string, ["id", "name"]));
                } else $nb_items = $statuses->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $statuses = $statuses->find($search_string, ["id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($statuses as $status) {
                    $data[$i]['id'] = $status->getId();
                    $data[$i]['name'] = $status->getName();
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

            $this->render(name_file: 'horses/statuses', params: $params, title: 'Statuses');
        }
    }

    #[Route('/statuses/form', 'statuses_form', ['GET', 'POST'])] public function statusesForm(Request $request)
    {
        $auth_table = "statuses";
        $link_table = "statuses";
        $page_title = "Statuses";
        $page_localisation = "horses/statuses_form";
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
                    $statuses = new StatusesModel();

                    if ($request->get->get('id')) {
                        $account = $statuses->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                ]);

                                $statuses->setName($request->post->get('name'))
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

                            $statuses->setName($request->post->get('name'))
                                ->create();

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
