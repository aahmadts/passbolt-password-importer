<?php

namespace PassboltPasswordImporter\Controller;


use App\Model\Entity\Permission;
use App\Model\Table\ProfilesTable;
use App\Model\Table\ResourcesTable;
use App\Model\Table\SecretsTable;
use App\Model\Table\UsersTable;
use App\Utility\Gpg;
use Cake\ORM\TableRegistry;

class ImportController extends AppController
{
    const INDEX_URL = 0;
    const INDEX_USERNAME = 1;
    const INDEX_PASSWORD = 2;
    const INDEX_EXTRA = 3;
    const INDEX_NAME = 4;
    /* @var ResourcesTable $resourceTable */
    private $resourceTable;
    /* @var SecretsTable $secretTable */
    private $secretTable;
    /* @var UsersTable $userTable */
    private $userTable;
    /* @var ProfilesTable $profileTable */
    private $profileTable;
    /* @var $userGpgKeyInfo \App\Model\Entity\Gpgkey */
    private $userGpgKeyInfo;

    public function index()
    {
        $this->init();

        $headers = $this->request->getHeaders();
        $files = $this->request->getUploadedFiles();

        $response = [];
        /* @var $file \Zend\Diactoros\UploadedFile */
        foreach ($files as $file) {
            $filename = $file->getClientFilename();

            $fileHeaders = ['url', 'username', 'password', 'extra', 'name'];

            try {
                $fileContent = $file->getStream();
                $fileContentExplodedPerLine = explode(PHP_EOL, $fileContent);
                $explodedFirstRow = explode(',', $fileContentExplodedPerLine[0]);

                if (!array_diff($fileHeaders, $explodedFirstRow)) {
                    array_shift($fileContentExplodedPerLine);
                }

                foreach ($fileContentExplodedPerLine as $key => $fileRow) {


                    if (empty($fileContent) || $fileRow == '') {
                        continue;
                    } else {
                        $fileRow = str_getcsv($fileRow);
                        $resource = $this->resourceExists($fileRow);

                        //$response['report'][$key] ['existence'] = json_encode($resource) ;

                        if (empty($resource)) {
                            $resource = $this->savePassword($fileRow);

                        //$response['report'][$key] ['save'] = json_encode($resource) ;

                            if (empty($resource)) {
                        //$response['report'][$key] ['save_error'] = $fileRow ;
                                $response['error'][] = $this->getErrorPointer($fileRow) ;
                                continue;
                            }

                            $response['imported'][] = $resource->name ?: json_encode($resource);

                        } else {

                            $response['exist'][] =  '[owner: ' . $resource->owner. '] ' . $resource->name ;
                        }
                    }
                }

            } catch (\Exception $e) {
                $response['bad_files'][] = $filename .': '. (string)$e->getMessage();
            }
        }

        return $this->response
            ->withType("text/json")
            ->withStringBody(json_encode($response));

    }

    private function getErrorPointer($fileRow)
    {
        if (isset($fileRow[self::INDEX_NAME])) {
            return $fileRow[self::INDEX_NAME];
        } elseif (isset($fileRow[self::INDEX_URL])) {
            return $fileRow[self::INDEX_URL];
        } elseif (isset($fileRow[self::INDEX_USERNAME])) {
            return $fileRow[self::INDEX_USERNAME];
        } elseif (isset($fileRow[self::INDEX_EXTRA])) {
            return $fileRow[self::INDEX_EXTRA];
        } else {
            return json_encode($fileRow);
        }
    }

    private function init()
    {
        $this->resourceTable = TableRegistry::get('Resources');
        $this->secretTable = TableRegistry::get('Secrets');
        $this->userTable = TableRegistry::get('Users');
        $this->profileTable = TableRegistry::get('Profiles');
        $this->loadModel('Permissions');
        $this->loadModel('Resources');
        $this->loadModel('Users');
        $this->userGpgKeyInfo = $this->getUserGpgKeyInfo();
    }

    private function savePassword($fileRow)
    {
        $encryptedData = isset($fileRow[self::INDEX_PASSWORD]) ? $this->encryptData($fileRow[self::INDEX_PASSWORD]) : '';

        $data = [
            'description' => isset($fileRow[self::INDEX_EXTRA]) ? $fileRow[self::INDEX_EXTRA] : '',
            'name' => isset($fileRow[self::INDEX_NAME]) &&  $fileRow[self::INDEX_NAME] != '' ? $fileRow[self::INDEX_NAME] : "RENAME_ME",
            'secrets' => [
                [
                    'data' => $encryptedData,
                    'user_id' => $this->User->id() ?: '',
                ],
            ],
            'uri' => isset($fileRow[self::INDEX_URL]) ?  $fileRow[self::INDEX_URL] : '',
            'username' => isset($fileRow[self::INDEX_USERNAME]) ? $fileRow[self::INDEX_USERNAME] : ''
        ];

        $resourceEntity = $this->_buildEntity($data);

        return $this->Resources->save($resourceEntity);
    }

    private function encryptData($data)
    {
        $userArmoredKey = $this->userGpgKeyInfo->armored_key;

        $gpg = new Gpg();
        $gpg->setEncryptKey($userArmoredKey);

        return $gpg->encrypt($data);
    }

    private function getUserGpgKeyInfo()
    {
        /* @var $user \App\Model\Entity\User */
        $user = $this->Users->findView($this->User->id(), $this->User->role())->first();
        return $user->get('gpgkey');
    }

    /*
     * mostly copied from \App\Controller\Resources\ResourcesAddController::_buildAndValidateEntity
     */
    protected function _buildEntity($data)
    {
        // Enforce data.
        $data['created_by'] = $this->User->id();
        $data['modified_by'] = $this->User->id();
        $data['permissions'] = [
            [
                'aro' => 'User',
                'aro_foreign_key' => $this->User->id(),
                'aco' => 'Resource',
                'type' => Permission::OWNER,
            ]
        ];
        // If no secrets given, a specific message is returned.
        if (isset($data['secrets'])) {
            $data['secrets'][0]['user_id'] = $this->User->id();
        }

        // Build entity and perform basic check
        $resource = $this->Resources->newEntity($data, [
            'accessibleFields' => [
                'name' => true,
                'username' => true,
                'uri' => true,
                'description' => true,
                'created_by' => true,
                'modified_by' => true,
                'secrets' => true,
                'permissions' => true
            ],
            'associated' => [
                'Permissions' => [
                    'validate' => 'saveResource',
                    'accessibleFields' => [
                        'aco' => true,
                        'aro' => true,
                        'aro_foreign_key' => true,
                        'type' => true
                    ]
                ],
                'Secrets' => [
                    'validate' => 'saveResource',
                    'accessibleFields' => [
                        'user_id' => true,
                        'data' => true
                    ]
                ]
            ]
        ]);

        return $resource;
    }

    private function resourceExists($resource)
    {
        $whereCriteria = [];

        if (isset($resource[self::INDEX_USERNAME]) && $resource[self::INDEX_USERNAME] != '') {
            $whereCriteria['username'] = $resource[self::INDEX_USERNAME];
        }
        if (isset($resource[self::INDEX_URL]) && $resource[self::INDEX_URL] != '') {
            $whereCriteria['uri'] = $resource[self::INDEX_URL];
        }
        if (isset($resource[self::INDEX_EXTRA]) && $resource[self::INDEX_EXTRA] != '') {
            $whereCriteria['description'] = $resource[self::INDEX_EXTRA];
        }
        $whereCriteria['deleted'] = false;

        /* @var \App\Model\Entity\Resource $resourceResult */
        $resourceResult = $this->resourceTable
            ->find()
            ->where($whereCriteria)
            ->first();

        if (empty($resourceResult)) {

            return false;

        } else {
            /* @var \App\Model\Entity\Secret $secret */
            $secret = $this->secretTable->find()->where([
                'resource_id' => $resourceResult->id,
            ])->first();

            if (empty($secret)) {

                return false;

            } else {
                /* @var \App\Model\Entity\Profile $profile */
                $profile = $this->profileTable->find()->where([
                    'user_id' => $secret->user_id
                ])->first();

                if (empty($profile)) {
                    return $resourceResult;
                }
                $resourceResult->set('owner', $profile->last_name);

                return $resourceResult;
                }
        }
    }
}