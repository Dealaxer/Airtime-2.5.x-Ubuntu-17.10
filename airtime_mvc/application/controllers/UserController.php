<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-hosts', 'json')
                    ->addActionContext('get-user-data-table-info', 'json')
                    ->addActionContext('get-user-data', 'json')
                    ->addActionContext('remove-user', 'json')
                    ->addActionContext('edit-user', 'json')
                    ->initContext();
    }

    public function addUserAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $js_files = array(
            'js/datatables/js/jquery.dataTables.js?',
            'js/datatables/plugin/dataTables.pluginAPI.js?',
            'js/airtime/user/user.js?'
        );

        foreach ($js_files as $js) {
            $this->view->headScript()->appendFile(
                $baseUrl.$js.$CC_CONFIG['airtime_version'],'text/javascript');
        }

        $this->view->headLink()->appendStylesheet($baseUrl.'css/users.css?'.$CC_CONFIG['airtime_version']);

        $form = new Application_Form_AddUser();

        $this->view->successMessage = "";

        if ($request->isPost()) {
            $params = $request->getPost();
            $postData = explode('&', $params['data']);
            $formData = array();
            foreach($postData as $k=>$v) {
                $v = explode('=', $v);
                $formData[$v[0]] = urldecode($v[1]);
            }

            if ($form->isValid($formData)) {

                if ($form->validateLogin($formData)) {
                    $user = new Application_Model_User($formData['user_id']);
                    if (empty($formData['user_id'])) {
                        $user->setLogin($formData['login']);
                    }
                    $user->setFirstName($formData['first_name']);
                    $user->setLastName($formData['last_name']);
                    // We don't allow 6 x's as a password.
                    // The reason is because we that as a password placeholder
                    // on the client side.
                    if ($formData['password'] != "xxxxxx") {
                        $user->setPassword($formData['password']);
                    }
                    $user->setType($formData['type']);
                    $user->setEmail($formData['email']);
                    $user->setCellPhone($formData['cell_phone']);
                    $user->setSkype($formData['skype']);
                    $user->setJabber($formData['jabber']);
                    $user->save();

                    $form->reset();
                    $this->view->form = $form;

                    if (strlen($formData['user_id']) == 0) {
                        $this->view->successMessage = "<div class='success'>"._("User added successfully!")."</div>";
                    } else {
                        $this->view->successMessage = "<div class='success'>"._("User updated successfully!")."</div>";
                    }

                    $this->_helper->json->sendJson(array("valid"=>"true", "html"=>$this->view->render('user/add-user.phtml')));
                } else {
                    $this->view->form = $form;
                    $this->_helper->json->sendJson(array("valid"=>"false", "html"=>$this->view->render('user/add-user.phtml')));
                }
            } else {
                $this->view->form = $form;
                $this->_helper->json->sendJson(array("valid"=>"false", "html"=>$this->view->render('user/add-user.phtml')));
            }
        }

        $this->view->form = $form;
    }

    public function getHostsAction()
    {
        $search            = $this->_getParam('term');
        $this->view->hosts = Application_Model_User::getHosts($search);
    }

    public function getUserDataTableInfoAction()
    {
        $post = $this->getRequest()->getPost();
        $users = Application_Model_User::getUsersDataTablesInfo($post);

        $this->_helper->json->sendJson($users);
    }

    public function getUserDataAction()
    {
        $id = $this->_getParam('id');
        $this->view->entries = Application_Model_User::GetUserData($id);
    }
    
    public function editUserAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_EditUser();
        if ($request->isPost()) {
            $formData = $request->getPost();
            
            if ($form->isValid($formData) &&
                       $form->validateLogin($formData['cu_login'], $formData['cu_user_id'])) {
                $user = new Application_Model_User($formData['cu_user_id']);
                $user->setFirstName($formData['cu_first_name']);
                $user->setLastName($formData['cu_last_name']);
                // We don't allow 6 x's as a password.
                // The reason is because we use that as a password placeholder
                // on the client side.
                if ($formData['cu_password'] != "xxxxxx") {
                    $user->setPassword($formData['cu_password']);
                }
                $user->setEmail($formData['cu_email']);
                $user->setCellPhone($formData['cu_cell_phone']);
                $user->setSkype($formData['cu_skype']);
                $user->setJabber($formData['cu_jabber']);
                $user->save();

                Application_Model_Preference::SetUserLocale($formData['cu_locale']);
                Application_Model_Preference::SetUserTimezone($formData['cu_timezone']);

                //configure localization with new locale setting
                Application_Model_Locale::configureLocalization($formData['cu_locale']);
                //reinitialize form so language gets translated
                $form = new Application_Form_EditUser();

                $this->view->successMessage = "<div class='success'>"._("Settings updated successfully!")."</div>";
            }
            $this->view->form = $form;
            $this->view->html = $this->view->render('user/edit-user.phtml');
        }
        $this->view->form = $form;
        $this->view->html = $this->view->render('user/edit-user.phtml');
    }

    public function removeUserAction()
    {
        // action body
        $delId = $this->_getParam('id');
        $valid_actions = array("delete_cascade", "reassign_to");
        $files_action = $this->_getParam('deleted_files');

        # TODO : remove this. we only use default for now not to break the UI.
        if (!$files_action) { # set default action
            $files_action = "reassign_to";
            $new_owner    = Application_Model_User::getFirstAdmin();
        }

        # only delete when valid action is selected for the owned files
        if (! in_array($files_action, $valid_actions) ) {
            return;
        }

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $userId = $userInfo->id;

        # Don't let users delete themselves
        if ($delId == $userId) {
            return;
        }

        $user = new Application_Model_User($delId);

        # Take care of the user's files by either assigning them to somebody
        # or deleting them all
        if ($files_action == "delete_cascade") {
            $user->deleteAllFiles();
        } elseif ($files_action == "reassign_to") {
            // TODO : fix code to actually use the line below and pick a
            // real owner instead of defaulting to the first found admin
            //$new_owner_id = $this->_getParam("new_owner");
            //$new_owner    = new Application_Model_User($new_owner_id);
            $user->donateFilesTo( $new_owner );
            Logging::info("Reassign to user {$new_owner->getDbId()}");
        }
        # Finally delete the user
        $this->view->entries = $user->delete();
    }
}
