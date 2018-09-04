<?php

namespace PassboltPasswordImporter\Controller;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{

    public function index()
    {
        $this->viewBuilder()
            ->setLayout('default');
    }
}
