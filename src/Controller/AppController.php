<?php

namespace PassboltPasswordImporter\Controller;

use App\Controller\AppController as BaseController;
use App\Model\Table\GroupsTable;
use App\Model\Table\GroupsUsersTable;

class AppController extends BaseController
{

    public function index()
    {
        $this->loadModel('Groups');
        /* @var GroupsTable $groupsTable */
        $groupsTable =  \Cake\ORM\TableRegistry::get('Groups');
        /* @var Cake\ORM\ResultSet $groups*/
        $groups = $groupsTable->find()->all();
        /* @var App\Model\Entity\Group $group*/
        $this->set(['allGroups' => $groups ]);

        $this->viewBuilder()
            ->setLayout('default');
    }
}
