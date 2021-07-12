<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\Classes\Validator;
use App\Core\System\Controller;
use App\Models\Item_TypesModel;
use App\Models\ItemsModel;

final class ItemsController extends Controller {

    #[Route('/items', 'items', ['GET', 'POST'])] public function index(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("items", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("items", $tables)) {
                    $position = array_search("items", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $items = new ItemsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $items->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'Items');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($items->countLike($search_string, ["id", "item_type_id", "description", "level"]));
                } else $nb_items = $items->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $items = $items->find($search_string, ["id", "item_type_id", "description", "level"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($items as $item) {
                    $data[$i]['id'] = $item->getId();
                    $data[$i]['item_type_id'] = $item->getItemTypeId();
                    $data[$i]['description'] = $item->getDescription();
                    $data[$i]['level'] = $item->getLevel();
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

            $this->render(name_file: 'items/index', params: $params, title: 'Items');
        };
    }

    #[Route('/items/form', 'items_form', ['GET', 'POST'])] public function indexForm(Request $request)
    {
        $auth_table = "items";
        $link_table = "items";
        $this_table = "items_form";
        $page_title = "Items";
        $page_localisation = "items/index_form";
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
                    $items = new ItemsModel();
                    $item_type = new Item_TypesModel();

                    if ($request->get->get('id')) {
                        $account = $items->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'item_type_id' => ['required'],
                                    'description' => ['required'],
                                    'level' => ['required'],
                                    'price' => ['required'],
                                    'on_sale' => ['required'],
                                ]);

                                if (!$item_type->findById($request->post->get('item_type_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $items->setItemTypeId($request->post->get('item_type_id'))
                                        ->setDescription($request->post->get('description'))
                                        ->setLevel($request->post->get('level'))
                                        ->setPrice($request->post->get('price'))
                                        ->setOnSale($request->post->get('on_sale'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getItemTypeId();
                            $data[] = $account->getDescription();
                            $data[] = $account->getLevel();
                            $data[] = $account->getPrice();
                            $data[] = $account->getOnSale();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'item_type_id' => ['required'],
                                'description' => ['required'],
                                'level' => ['required'],
                                'price' => ['required'],
                                'on_sale' => ['required'],
                            ]);

                            if (!$item_type->findById($request->post->get('item_type_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $items->setItemTypeId($request->post->get('item_type_id'))
                                    ->setDescription($request->post->get('description'))
                                    ->setLevel($request->post->get('level'))
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

    #[Route('/items/types', 'items_types', ['GET', 'POST'])] public function itemsTypes(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("item_types", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("item_types", $tables)) {
                    $position = array_search("item_types", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $item_types = new Item_TypesModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $item_types->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'items/types');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($item_types->countLike($search_string, ["item_type_id", "name"]));
                } else $nb_items = $item_types->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $item_types = $item_types->find($search_string, ["item_type_id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($item_types as $item_type) {
                    $data[$i]['item_type_id'] = $item_type->getId();
                    $data[$i]['name'] = $item_type->getName();
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

            $this->render(name_file: 'items/items_types', params: $params, title: 'Items types');
        };
    }

    #[Route('/items/types/form', 'items_types_form', ['GET', 'POST'])] public function itemsTypesForm(Request $request)
    {
        $auth_table = "statuses";
        $link_table = "items_types";
        $page_title = "Items types";
        $page_localisation = "items/items_types_form";
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
                    $item_type = new Item_TypesModel();

                    if ($request->get->get('id')) {
                        $account = $item_type->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                ]);

                                $item_type->setName($request->post->get('name'))
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

                            $item_type->setName($request->post->get('name'))
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
