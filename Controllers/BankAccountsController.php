<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\System\Controller;
use App\Core\System\Model;
use App\Models\Bank_Account_HistoryModel;
use App\Models\Bank_AccountsModel;
use App\Core\Classes\Validator;
use App\Models\PlayersModel;

final class BankAccountsController extends Controller {

    #[Route('/bank', 'bank_accounts', ['GET', 'POST'])] public function index(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("bank_accounts", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("bank_accounts", $tables)) {
                    $position = array_search("bank_accounts", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $bank_accounts = new Bank_AccountsModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $bank_accounts->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'bank');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($bank_accounts->countLike($search_string, ["id", "player_id", "balance"]));
                } else $nb_items = $bank_accounts->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $bank_accounts = $bank_accounts->find($search_string, ["id", "player_id", "balance"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($bank_accounts as $bank_account) {
                    $data[$i]['id'] = $bank_account->getId();
                    $data[$i]['player_id'] = $bank_account->getPlayerId();
                    $data[$i]['balance'] = $bank_account->getBalance();
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

            $this->render(name_file: 'bank/index', params: $params, title: 'Bank accounts');
        };
    }

    #[Route('/bank/form', 'bank_accounts_form', ['GET', 'POST'])] public function bankAccountForm(Request $request)
    {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("bank_accounts", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("bank_accounts", $tables)) {
                    $position = array_search("bank_accounts", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
                if (!$this->permissions("INSERT", $permissions) && !$this->permissions("UPDATE", $permissions)) {
                    $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour ajouter ou modifier les données de cette table.");
                    $this->redirect(self::reverse("bank_accounts"));
                } else {
                    $validator = new Validator($_POST);
                    $bank_accounts = new Bank_AccountsModel();
                    $player = new PlayersModel();

                    if ($request->get->get('id')) {
                        $account = $bank_accounts->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse('bank_accounts'));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'player_id' => ['required'],
                                    'balance' => ['required'],
                                ]);

                                if (!$player->findById($request->post->get('player_id'))) {
                                    $this->addFlash('error', "Ce Player ID n'existe pas.");
                                    $this->redirect("/bank/form?id=".$request->get->get('id'));
                                } else {
                                    $bank_accounts->setPlayerId($request->post->get('player_id'))
                                        ->setBalance($request->post->get('balance'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse('bank_accounts'));
                                }
                            }

                            $data[] = $account->getPlayerId();
                            $data[] = $account->getBalance();

                            $this->render(name_file: 'bank/index_form', params: [
                                "data"=> $data,
                            ], title: 'Bank accounts');
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'player_id' => ['required'],
                                'balance' => ['required'],
                            ]);

                            if (!$player->findById($request->post->get('player_id'))) {
                                $this->addFlash('error', "Ce Player ID n'existe pas.");
                                $this->redirect(self::reverse('bank_accounts_form'));
                            } else {
                                $bank_accounts->setPlayerId($request->post->get('player_id'))
                                    ->setBalance($request->post->get('balance'))
                                    ->create();

                                $this->addFlash('success', "Les données ont été ajouté dans la table.");
                                $this->redirect(self::reverse('bank_accounts'));
                            }
                        }

                        $this->render(name_file: 'bank/index_form', title: 'Bank accounts');
                    }
                }
            }
        }
    }

    #[Route('/bank/history', 'bank_account_history', ['GET', 'POST'])] public function bankAccountHistory(Request $request) {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("bank_account_history", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("bank_account_history", $tables)) {
                    $position = array_search("bank_account_history", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
            }

            $params = [
                'permissions'=> $permissions,
            ];

            if ($this->permissions("SELECT", $permissions)) {
                $bank_account_history = new Bank_Account_HistoryModel();

                if(isset($_POST['row'])) {
                    if(isset($_POST['delete'])) {
                        $i = 0;
                        foreach ($_POST['row'] as $row) {
                            $i++;
                            $bank_account_history->delete($row);
                        }
                        $this->addFlash('success', "{$i} entrées supprimées");
                        $this->redirect(header: 'bank');
                    }
                }

                $data = [];

                $search_string = "";
                $filter = "";
                $order = "";
                if(isset($_GET['search'])) {
                    $search_string = $_GET['search'];
                    $nb_items = count($bank_account_history->countLike($search_string, ["id", "bank_account_id", "action", "amount", "label", "date"]));
                } else $nb_items = $bank_account_history->countAll()->nb_items;
                if(isset($_GET['filter'])) $filter = $_GET['filter'];
                if(isset($_GET['order'])) $order = $_GET['order'];

                $last_page = ceil($nb_items/NB_PER_PAGE);
                $current_page = 1;
                if(isset($_GET['page'])) $current_page = $_GET['page'] >= 1 && $_GET['page'] <= $last_page ? $_GET['page'] : 1;
                if(isset($_POST['page'])) $current_page = $_POST['page'] >= 1 && $_POST['page'] <= $last_page ? $_POST['page'] : 1;
                $first_of_page = ($current_page * NB_PER_PAGE) - NB_PER_PAGE;
                $bank_account_history = $bank_account_history->find($search_string, ["id", "bank_account_id", "action", "amount", "label", "date"], $first_of_page, NB_PER_PAGE, $filter, $order);

                $i = 0;

                foreach ($bank_account_history as $row) {
                    $data[$i]['id'] = $row->getId();
                    $data[$i]['bank_account_id'] = $row->getBankAccountId();
                    $data[$i]['action'] = $row->getAction();
                    $data[$i]['amount'] = $row->getAmount();
                    $data[$i]['label'] = $row->getLabel();
                    $data[$i]['date'] = $row->getDate();
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

            $this->render(name_file: 'bank/bank_history', params: $params, title: 'Bank account history');
        };
    }

    #[Route('/bank/history/form', 'bank_account_history_form', ['GET', 'POST'])] public function bankAccountHistoryForm(Request $request)
    {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }
            if (!$this->permissions("bank_account_history", $tables)) {
                $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour accéder à cette table.");
                $this->redirect(self::reverse('home'));
            } else {
                if (in_array("bank_account_history", $tables)) {
                    $position = array_search("bank_account_history", $tables);
                } elseif (in_array("*", $tables)) {
                    $position = array_search("*", $tables);
                }
                $permissions = $_SESSION["authorizations"][$position]["permissions"];
                if (!$this->permissions("INSERT", $permissions)) {
                    $this->addFlash('error', "Vous n'avez pas les permissions suffisantes pour ajouter des données à cette table.");
                    $this->redirect(self::reverse('bank_account_history'));
                } else {
                    $validator = new Validator($_POST);
                    $bank_accounts = new Bank_AccountsModel();
                    $bank_accounts_history = new Bank_Account_HistoryModel();

                    if ($request->get->get('id')) {
                        $account = $bank_accounts_history->findById($request->get->get('id'));

                        if (!$account) {
                            $this->addFlash('error', "Cet ID n'existe pas.");
                            $this->redirect(self::reverse('bank_account_history'));
                        } else {
                            if($validator->isSubmitted('update')) {
                                $validator->validate([
                                    'bank_account_id' => ['required'],
                                    'action' => ['required'],
                                    'amount' => ['required'],
                                    'label' => ['required'],
                                    'date' => ['required'],
                                ]);

                                if (!$bank_accounts->findById($request->post->get('bank_account_id'))) {
                                    $this->addFlash('error', "Ce Bank Account ID n'existe pas.");
                                    $this->redirect("/bank/history/form?id=".$request->get->get('id'));
                                } else {
                                    $bank_accounts_history->setBankAccountId($request->post->get('bank_account_id'))
                                        ->setAction($request->post->get('action'))
                                        ->setAmount($request->post->get('amount'))
                                        ->setLabel($request->post->get('label'))
                                        ->setDate($request->post->get('date'))
                                        ->update($request->get->get('id'));

                                    $this->addFlash('success', "Les données ont été modifiées.");
                                    $this->redirect(self::reverse('bank_account_history'));
                                }
                            }

                            $data[] = $account->getBankAccountId();
                            $data[] = $account->getAction();
                            $data[] = $account->getAmount();
                            $data[] = $account->getLabel();
                            $data[] = $account->getDate();

                            $this->render(name_file: 'bank/bank_history_form', params: [
                                "data"=> $data,
                            ], title: 'Bank accounts');
                        }
                    } else {
                        if($validator->isSubmitted('insert')) {
                            $validator->validate([
                                'bank_account_id' => ['required'],
                                'action' => ['required'],
                                'amount' => ['required'],
                                'label' => ['required'],
                                'date' => ['required'],
                            ]);

                            if (!$bank_accounts->findById($request->post->get('bank_account_id'))) {
                                $this->addFlash('error', "Ce Bank Account ID n'existe pas.");
                                $this->redirect(self::reverse('bank_account_history_form'));
                            } else {
                                $bank_accounts_history->setBankAccountId($request->post->get('bank_account_id'))
                                    ->setAction($request->post->get('action'))
                                    ->setAmount($request->post->get('amount'))
                                    ->setLabel($request->post->get('label'))
                                    ->setDate($request->post->get('date'))
                                    ->create();

                                $this->addFlash('success', "Les données ont été ajouté dans la table.");
                                $this->redirect(self::reverse('bank_account_history'));
                            }
                        }

                        $this->render(name_file: 'bank/bank_history_form', title: 'Bank account history');
                    }
                }
            }
        }
    }
}
