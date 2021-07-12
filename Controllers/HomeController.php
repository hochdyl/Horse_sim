<?php

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\System\Controller;

final class HomeController extends Controller {

    #[Route('/', 'home')] public function index() {
        if (!$this->isAuthenticated()) {
            $this->redirect(self::reverse('login'));
        } else {
            foreach ($_SESSION["authorizations"] as $authorizations) {
                $tables[] = $authorizations["table"];
            }

            $this->render(name_file: 'home', params: [
                'tables'=> $tables
            ]);
        };
    }

}
