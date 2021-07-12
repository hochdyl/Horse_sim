<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\Classes\Validator;
use App\Core\System\Controller;
use App\Models\HorsesModel;
use App\Models\Player_HorsesModel;
use App\Models\PlayersModel;

final class PlayersController extends Controller {

    #[Route('/players', 'players', ['GET', 'POST'])] public function index(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("players", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("players", $tables)) {
                    $position = array_search("players", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $players = new PlayersModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $players->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'players');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($players->countLike($search_string, ["id", "nickname", "mail"]));
                } else $nb_items = $players->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $players = $players->find($search_string, ["id", "nickname", "mail"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($players as $player) {
                    $data[$i]['id'] = $player->getId();
                    $data[$i]['nickname'] = $player->getNickname();
                    $data[$i]['mail'] = $player->getMail();
                    $data[$i]['logInDateTime'] = $player->getLogInDatetime();
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

            $this->render(name_file: 'players/index', params: $params, title: 'Players');
        };
    }

    #[Route('/players/form', 'players_form', ['GET', 'POST'])] public function indexForm(Request $request)
    {
        $auth_table = "players";
        $link_table = "players";
        $page_title = "Players";
        $page_localisation = "players/index_form";
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
                    $players = new PlayersModel();

                    if ($request->get->get('id')) {
                        $account = $players->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'nickname' => ['required'],
                                    'mail' => ['required'],
                                    'password' => ['required'],
                                    'last_name' => ['required'],
                                    'first_name' => ['required'],
                                    'sexe' => ['required'],
                                    'phone' => ['required'],
                                    'street' => ['required'],
                                    'city' => ['required'],
                                    'zip_code' => ['required'],
                                    'country' => ['required'],
                                    'avatar' => ['required'],
                                    'description' => ['required'],
                                    'website' => ['required'],
                                    'ip_address' => ['required'],
                                ]);

                                $players->setNickname($request->post->get('nickname'))
                                    ->setMail($request->post->get('mail'))
                                    ->setPassword($request->post->get('password'))
                                    ->setLastName($request->post->get('last_name'))
                                    ->setFirstName($request->post->get('first_name'))
                                    ->setSexe($request->post->get('sexe'))
                                    ->setPhone($request->post->get('phone'))
                                    ->setStreet($request->post->get('street'))
                                    ->setCity($request->post->get('city'))
                                    ->setZipCode($request->post->get('zip_code'))
                                    ->setCountry($request->post->get('country'))
                                    ->setAvatar($request->post->get('avatar'))
                                    ->setDescription($request->post->get('description'))
                                    ->setWebsite($request->post->get('website'))
                                    ->setIpAddress($request->post->get('ip_address'))
                                    ->update($request->get->get('id'));

                                $this->addFlash('success', "Les données ont été modifiées.");
                                $this->redirect(self::reverse($link_table));
                            }

                            $data[] = $account->getNickname();
                            $data[] = $account->getMail();
                            $data[] = $account->getPassword();
                            $data[] = $account->getLastName();
                            $data[] = $account->getFirstName();
                            $data[] = $account->getSexe();
                            $data[] = $account->getPhone();
                            $data[] = $account->getStreet();
                            $data[] = $account->getCity();
                            $data[] = $account->getZipCode();
                            $data[] = $account->getCountry();
                            $data[] = $account->getAvatar();
                            $data[] = $account->getDescription();
                            $data[] = $account->getWebsite();
                            $data[] = $account->getIpAddress();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'nickname' => ['required'],
                                'mail' => ['required'],
                                'password' => ['required'],
                                'last_name' => ['required'],
                                'first_name' => ['required'],
                                'sexe' => ['required'],
                                'phone' => ['required'],
                                'street' => ['required'],
                                'city' => ['required'],
                                'zip_code' => ['required'],
                                'country' => ['required'],
                                'avatar' => ['required'],
                                'description' => ['required'],
                                'website' => ['required'],
                                'ip_address' => ['required'],
                            ]);

                            $players->setNickname($request->post->get('nickname'))
                                ->setMail($request->post->get('mail'))
                                ->setPassword($request->post->get('password'))
                                ->setLastName($request->post->get('last_name'))
                                ->setFirstName($request->post->get('first_name'))
                                ->setSexe($request->post->get('sexe'))
                                ->setPhone($request->post->get('phone'))
                                ->setStreet($request->post->get('street'))
                                ->setCity($request->post->get('city'))
                                ->setZipCode($request->post->get('zip_code'))
                                ->setCountry($request->post->get('country'))
                                ->setAvatar($request->post->get('avatar'))
                                ->setDescription($request->post->get('description'))
                                ->setWebsite($request->post->get('website'))
                                ->setIpAddress($request->post->get('ip_address'))
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

    #[Route('/player/horses', 'player_horses', ['GET', 'POST'])] public function playerHorses(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("player_horses", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("player_horses", $tables)) {
                    $position = array_search("player_horses", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $player_horses = new Player_HorsesModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $playerid = $ids[0];
                            $horseid = $ids[1];
                            $player_horses->query("DELETE FROM {$player_horses->getTableName()} WHERE player_id = $playerid AND horse_id = $horseid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'player/horses');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($player_horses->countLike($search_string, ["player_id", "horse_id"]));
                } else $nb_items = $player_horses->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $player_horses = $player_horses->find($search_string, ["player_id", "horse_id"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($player_horses as $player_horse) {
                    $data[$i]['playerid'] = $player_horse->getPlayerId();
                    $data[$i]['horseid'] = $player_horse->getHorseId();
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

            $this->render(name_file: 'players/player_horses', params: $params, title: 'Players horses');
        };
    }

    #[Route('/player/horses/form', 'player_horses_form', ['GET', 'POST'])] public function playerHorsesForm(Request $request)
    {
        $auth_table = "player_horses";
        $link_table = "player_horses";
        $this_table = "player_horses_form";
        $page_title = "Players horses";
        $page_localisation = "players/player_horses_form";
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
                    $player_horses = new Player_HorsesModel();
                    $players = new PlayersModel();
                    $horses = new HorsesModel();

                    $player_id = $request->get->get('playerid');
                    $horse_id = $request->get->get('horseid');

                    if ($player_id && $horse_id) {
                        $account = $player_horses->query("SELECT * FROM $auth_table WHERE player_id = $player_id AND horse_id = $horse_id LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'player_id' => ['required'],
                                    'horse_id' => ['required'],
                                ]);

                                if (!$players->findById($player_id) &&
                                    !$horses->findById($horse_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?playerid=".$player_id."&horseid=".$horse_id);
                                } else {
                                    $setPlayerId = $request->post->get('player_id');
                                    $setHorseId = $request->post->get('horse_id');

                                    $player_horses->query("UPDATE $auth_table SET player_id = $setPlayerId, horse_id = $setHorseId WHERE player_id = $player_id AND horse_id = $horse_id");

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getPlayerId();
                            $data[] = $account->getHorseId();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'player_id' => ['required'],
                                'horse_id' => ['required'],
                            ]);

                            if (!$players->findById($player_id) &&
                                !$horses->findById($horse_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $player_horses->setPlayerId($request->post->get('player_id'))
                                    ->setHorseId($request->post->get('horse_id'))
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
