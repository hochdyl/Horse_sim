<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\Classes\Validator;
use App\Core\System\Controller;
use App\Models\AdsModel;
use App\Models\NewsModel;
use App\Models\Newspaper_AdsModel;
use App\Models\NewspapersModel;
use App\Models\Upcoming_EventsModel;
use App\Models\WeathersModel;

final class NewspapersController extends Controller {

    #[Route('/newspapers', 'newspapers', ['GET', 'POST'])] public function index(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("newspapers", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("newspapers", $tables)) {
                    $position = array_search("newspapers", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $newspapers = new NewspapersModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $newspapers->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'newspapers');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($newspapers->countLike($search_string, ["id", "date"]));
                } else $nb_items = $newspapers->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $newspapers = $newspapers->find($search_string, ["id", "date"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($newspapers as $newspaper) {
                    $data[$i]['id'] = $newspaper->getId();
                    $data[$i]['date'] = $newspaper->getDate();
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

            $this->render(name_file: 'newspapers/index', params: $params, title: 'Newspapers');
        };
    }

    #[Route('/newspapers/form', 'newspapers_form', ['GET', 'POST'])] public function indexForm(Request $request)
    {
        $auth_table = "newspapers";
        $link_table = "newspapers";
        $this_table = "newspapers_form";
        $page_title = "Newspapers";
        $page_localisation = "newspapers/index_form";
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
                    $newspapers = new NewspapersModel();
                    $weathers = new WeathersModel();

                    if ($request->get->get('id')) {
                        $account = $newspapers->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'date' => ['required'],
                                    'weather_id' => ['required'],
                                ]);

                                if (!$weathers->findById($request->post->get('weather_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $newspapers->setDate($request->post->get('date'))
                                        ->setWeatherId($request->post->get('weather_id'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getDate();
                            $data[] = $account->getWeatherId();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'date' => ['required'],
                                'weather_id' => ['required'],
                            ]);

                            if (!$weathers->findById($request->post->get('weather_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $newspapers->setDate($request->post->get('date'))
                                    ->setWeatherId($request->post->get('weather_id'))
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

    #[Route('/newspapers/news', 'newspapers_news', ['GET', 'POST'])] public function newspapersNews(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("news", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("news", $tables)) {
                    $position = array_search("news", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $news = new NewsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $news->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'newspapers/news');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($news->countLike($search_string, ["id", "date", "name"]));
                } else $nb_items = $news->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $news = $news->find($search_string, ["id", "date", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($news as $row) {
                    $data[$i]['id'] = $row->getId();
                    $data[$i]['date'] = $row->getDate();
                    $data[$i]['name'] = $row->getName();
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

            $this->render(name_file: 'newspapers/news', params: $params, title: 'News');
        };
    }

    #[Route('/newspapers/news/form', 'newspapers_news_form', ['GET', 'POST'])] public function newspapersNewsForm(Request $request)
    {
        $auth_table = "news";
        $link_table = "newspapers_news";
        $page_title = "News";
        $page_localisation = "newspapers/news_form";
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
                    $news = new NewsModel();

                    if ($request->get->get('id')) {
                        $account = $news->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'date' => ['required'],
                                    'name' => ['required'],
                                    'description' => ['required'],
                                    'image' => ['required'],
                                ]);

                                $news->setDate($request->post->get('date'))
                                    ->setName($request->post->get('name'))
                                    ->setDescription($request->post->get('description'))
                                    ->setImage($request->post->get('image'))
                                    ->update($request->get->get('id'));

                                $this->addFlash('success', "Les données ont été modifiées.");
                                $this->redirect(self::reverse($link_table));
                            }

                            $data[] = $account->getDate();
                            $data[] = $account->getName();
                            $data[] = $account->getDescription();
                            $data[] = $account->getImage();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'date' => ['required'],
                                'name' => ['required'],
                                'description' => ['required'],
                                'image' => ['required'],
                            ]);

                            $news->setDate($request->post->get('date'))
                                ->setName($request->post->get('name'))
                                ->setDescription($request->post->get('description'))
                                ->setImage($request->post->get('image'))
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

    #[Route('/newspapers/ads', 'newspaper_ads', ['GET', 'POST'])] public function newspapersAds(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("newspaper_ads", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("newspaper_ads", $tables)) {
                    $position = array_search("newspaper_ads", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $newspaper_ads = new Newspaper_AdsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ids = explode("-", $row);
                            $newspaperid = $ids[0];
                            $adid = $ids[1];
                            $newspaper_ads->query("DELETE FROM {$newspaper_ads->get()} WHERE newspaper_id = $newspaperid AND ad_id = $adid");
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'newspapers/ads');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($newspaper_ads->countLike($search_string, ["newspaper_id", "ad_id"]));
                } else $nb_items = $newspaper_ads->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $newspaper_ads = $newspaper_ads->find($search_string, ["newspaper_id", "ad_id"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($newspaper_ads as $row) {
                    $data[$i]['newspaper_id'] = $row->getNewspaperId();
                    $data[$i]['ad_id'] = $row->getAdId();
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

            $this->render(name_file: 'newspapers/newspaper_ads', params: $params, title: 'Newspapers ads');
        };
    }

    #[Route('/newspapers/ads/form', 'newspaper_ads_form', ['GET', 'POST'])] public function newspapersAdsForm(Request $request)
    {
        $auth_table = "newspaper_ads";
        $link_table = "newspaper_ads";
        $this_table = "newspaper_ads_form";
        $page_title = "Newspapers ads";
        $page_localisation = "newspapers/newspaper_ads_form";
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
                    $newspaper_ads = new Newspaper_AdsModel();
                    $newspapers = new NewspapersModel();
                    $ads = new AdsModel();

                    $newspaper_id = $request->get->get('newspaper_id');
                    $ad_id = $request->get->get('ad_id');

                    if ($newspaper_id && $ad_id) {
                        $account = $newspaper_ads->query("SELECT * FROM $auth_table WHERE newspaper_id = '$newspaper_id' AND ad_id = '$ad_id' LIMIT 1")->fetch();

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'newspaper_id' => ['required'],
                                    'ad_id' => ['required'],
                                ]);

                                if (!$newspapers->findById($newspaper_id) &&
                                    !$ads->findById($ad_id)) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?newspaper_id=".$newspaper_id."&ad_id=".$ad_id);
                                } else {
                                    $setNewspaperId = $request->post->get('newspaper_id');
                                    $setAdId = $request->post->get('ad_id');
                                    $newspaper_ads->query("UPDATE $auth_table SET newspaper_id = '$setNewspaperId', ad_id = '$setAdId' WHERE newspaper_id = '$newspaper_id' AND ad_id = '$ad_id'");

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getNewspaperId();
                            $data[] = $account->getAdId();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'newspaper_id' => ['required'],
                                'ad_id' => ['required'],
                            ]);

                            if (!$newspapers->findById($newspaper_id) &&
                                !$ads->findById($ad_id)) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $newspaper_ads->setNewspaperId($request->post->get('newspaper_id'))
                                    ->setAdId($request->post->get('ad_id'))
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

    #[Route('/newspapers/upcoming', 'newspapers_upcoming', ['GET', 'POST'])] public function newspapersUpcoming(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("upcoming_events", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("upcoming_events", $tables)) {
                    $position = array_search("upcoming_events", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $upcoming_events = new Upcoming_EventsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $upcoming_events->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'newspapers/upcoming');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($upcoming_events->countLike($search_string, ["id", "newspaper_id", "name"]));
                } else $nb_items = $upcoming_events->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $upcoming_events = $upcoming_events->find($search_string, ["id", "newspaper_id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($upcoming_events as $upcoming_event) {
                    $data[$i]['id'] = $upcoming_event->getId();
                    $data[$i]['newspaper_id'] = $upcoming_event->getNewspaperId();
                    $data[$i]['name'] = $upcoming_event->getName();
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

            $this->render(name_file: 'newspapers/upcoming_events', params: $params, title: 'Upcoming events');
        };
    }

    #[Route('/newspapers/upcoming/form', 'newspapers_upcoming_form', ['GET', 'POST'])] public function newspapersUpcomingForm(Request $request)
    {
        $auth_table = "upcoming_events";
        $link_table = "newspapers_upcoming";
        $this_table = "newspapers_upcoming_form";
        $page_title = "Upcoming events";
        $page_localisation = "newspapers/upcoming_events_form";
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
                    $upcoming_events = new Upcoming_EventsModel();
                    $newspapers = new NewspapersModel();

                    if ($request->get->get('id')) {
                        $account = $upcoming_events->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'newspaper_id' => ['required'],
                                    'name' => ['required'],
                                    'description' => ['required'],
                                    'image' => ['required'],
                                ]);

                                if (!$newspapers->findById($request->post->get('newspaper_id'))) {
                                    $this->addFlash('error', "L'un des ID n'existe pas.");
                                    $this->redirect(self::reverse($link_table)."/form?id=".$request->get->get('id'));
                                } else {
                                    $upcoming_events->setNewspaperId($request->post->get('newspaper_id'))
                                        ->setName($request->post->get('name'))
                                        ->setDescription($request->post->get('description'))
                                        ->setImage($request->post->get('image'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse($link_table));
                                }
                            }

                            $data[] = $account->getNewspaperId();
                            $data[] = $account->getName();
                            $data[] = $account->getDescription();
                            $data[] = $account->getImage();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'newspaper_id' => ['required'],
                                'name' => ['required'],
                                'description' => ['required'],
                                'image' => ['required'],
                            ]);

                            if (!$newspapers->findById($request->post->get('newspaper_id'))) {
                                $this->addFlash('error', "L'un des ID n'existe pas.");
                                $this->redirect(self::reverse($this_table));
                            } else {
                                $upcoming_events->setNewspaperId($request->post->get('newspaper_id'))
                                    ->setName($request->post->get('name'))
                                    ->setDescription($request->post->get('description'))
                                    ->setImage($request->post->get('image'))
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

    #[Route('/ads', 'ads', ['GET', 'POST'])] public function ads(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("ads", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("ads", $tables)) {
                    $position = array_search("ads", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $ads = new AdsModel();

                if (isset($_POST['row'])) {
                    if (isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $ads->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'ads');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if (isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($ads->countLike($search_string, ["id", "name"]));
                } else $nb_items = $ads->countAll()->nb_items;
                if (isset($_GET['filter'])) $filter = $_GET['filter'];
                if (isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items / NB_PER_PAGE);
                $current_page = 1;
                if (isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if (isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $ads = $ads->find($search_string, ["id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($ads as $ad) {
                    $data[$i]['id'] = $ad->getId();
                    $data[$i]['name'] = $ad->getName();
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

            $this->render(name_file: 'newspapers/ads', params: $params, title: 'Ads');
        };
    }

    #[Route('/ads/form', 'ads_form', ['GET', 'POST'])] public function adsForm(Request $request)
    {
        $auth_table = "ads";
        $link_table = "ads";
        $page_title = "Ads";
        $page_localisation = "newspapers/ads_form";
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
                    $ads = new AdsModel();

                    if ($request->get->get('id')) {
                        $account = $ads->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                    'description' => ['required'],
                                    'image' => ['required'],
                                ]);

                                $ads->setName($request->post->get('name'))
                                    ->setDescription($request->post->get('description'))
                                    ->setImage($request->post->get('image'))
                                    ->update($request->get->get('id'));

                                $this->addFlash('success', "Les données ont été modifiées.");
                                $this->redirect(self::reverse($link_table));
                            }

                            $data[] = $account->getName();
                            $data[] = $account->getDescription();
                            $data[] = $account->getImage();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'name' => ['required'],
                                'description' => ['required'],
                                'image' => ['required'],
                            ]);

                            $ads->setName($request->post->get('name'))
                                ->setDescription($request->post->get('description'))
                                ->setImage($request->post->get('image'))
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

    #[Route('/weathers', 'weathers', ['GET', 'POST'])] public function weathers(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("weathers", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("weathers", $tables)) {
                    $position = array_search("weathers", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $weathers = new WeathersModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $weathers->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'weathers');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($weathers->countLike($search_string, ["id", "name"]));
                } else $nb_items = $weathers->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $weathers = $weathers->find($search_string, ["id", "name"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($weathers as $weather) {
                    $data[$i]['id'] = $weather->getId();
                    $data[$i]['name'] = $weather->getName();
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

            $this->render(name_file: 'newspapers/weathers', params: $params, title: 'Weathers');
        };
    }

    #[Route('/weathers/form', 'weathers_form', ['GET', 'POST'])] public function weathersForm(Request $request)
    {
        $auth_table = "weathers";
        $link_table = "weathers";
        $page_title = "Weathers";
        $page_localisation = "newspapers/weathers_form";
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
                    $weathers = new WeathersModel();

                    if ($request->get->get('id')) {
                        $account = $weathers->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse($link_table));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'name' => ['required'],
                                    'description' => ['required'],
                                    'image' => ['required'],
                                ]);

                                $weathers->setName($request->post->get('name'))
                                    ->setDescription($request->post->get('description'))
                                    ->setImage($request->post->get('image'))
                                    ->update($request->get->get('id'));

                                $this->addFlash('success', "Les données ont été modifiées.");
                                $this->redirect(self::reverse($link_table));
                            }

                            $data[] = $account->getName();
                            $data[] = $account->getDescription();
                            $data[] = $account->getImage();

                            $this->render(name_file: $page_localisation, params: [
                                "data"=> $data,
                            ], title: $page_title);
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'name' => ['required'],
                                'description' => ['required'],
                                'image' => ['required'],
                            ]);

                            $weathers->setName($request->post->get('name'))
                                ->setDescription($request->post->get('description'))
                                ->setImage($request->post->get('image'))
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
