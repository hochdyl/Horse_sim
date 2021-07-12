<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Classes\SuperGlobals\Request;
use App\Core\Classes\Token;
use App\Core\System\Controller;
use App\Core\Classes\Validator;
use App\Core\System\Model;
use App\Models\UserModel;
use JetBrains\PhpStorm\NoReturn;

class AccountController extends Controller {

    public function get_string_between($string, $start, $end) {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    #[Route('/login', 'login', ['GET', 'POST'])] public function login(Request $request) {
        if ($this->isAuthenticated()) $this->redirect(self::reverse('home'));;
        $mysql_user = new UserModel();
        $validator = new Validator($_POST);

        if ($validator->isSubmitted()) {
            $validator->validate([
                'username' => ['required'],
                'password' => ['required']
            ]);

            $user = $mysql_user->findUser($request->post->get('username'));

            if ($user) {
                $query = $mysql_user->connect($request->post->get('username'), $request->post->get('password'));

                if ($query != 1) {
                    $token = Token::generate();
                    $grants = $query->query('SHOW GRANTS FOR CURRENT_USER();')->fetchAll();

                    $authorizations = [];
                    for ($i = 1; $i < count($grants); $i++) {
                        $grant = array_values($grants[$i]);

                        $table = $this->get_string_between($grant[1], ' ON ', ' TO ');
                        $table = str_replace(array('`', '\\'), '', $table);
                        $table = explode('.', $table);

                        $permissions = $this->get_string_between($grant[1], 'GRANT ', ' ON ');
                        $permissions = explode(', ', $permissions);

                        $authorizations[$i-1]['db'] = $table[0];
                        $authorizations[$i-1]['table'] = $table[1];

                        foreach ($permissions as $permission) {
                            $authorizations[$i-1]['permissions'][] = $permission;
                        }
                    }

                    $request->cookie->set('token', $token);
                    $request->session->set('token', $token);
                    $request->session->set('username', $request->post->get('username'));
                    $request->session->set('password', $request->post->get('password'));
                    $request->session->set('authorizations', $authorizations);
                    $this->addFlash('success', 'Vous êtes à présent connecté avec le compe "'. $request->post->get('username') .'".');
                    $this->redirect(self::reverse('home'));
                } else {
                    $this->addFlash('error', "Les identifiants de connexion sont invalides.");
                }
            } else {
                $this->addFlash('error', 'Les identifiants de connexion sont invalides.');
            }
        }
        $this->render(name_file: 'account/login', title: 'Connexion');
    }

    #[NoReturn] #[Route('/logout', 'logout')] public function logout(Request $request) {
        if (!$this->isAuthenticated()) {
            ErrorController::error404();
        } else {
            $request->cookie->delete('token');
            setcookie('token', '', time() - INACTIVITY_TIME, '/');
            $request->session->delete(restart_session: true);

            $this->addFlash('success', "Vous avez été déconnecté avec succès !");
            $this->redirect(self::reverse('login'));
        }
    }

}
