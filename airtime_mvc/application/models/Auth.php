<?php

class Application_Model_Auth
{
    const TOKEN_LIFETIME = 'P2D'; // DateInterval syntax

    private function generateToken($action, $user_id)
    {
       $salt = md5("pro");
       $token = self::generateRandomString();

       $info = new CcSubjsToken();
       $info->setDbUserId($user_id);
       $info->setDbAction($action);
       $info->setDbToken(sha1($token.$salt));
       $info->setDbCreated(gmdate('Y-m-d H:i:s'));
       $info->save();

       Logging::debug("generated token {$token}");

       return $token;
    }

    public function sendPasswordRestoreLink($user, $view)
    {
        $token = $this->generateToken('password.restore', $user->getDbId());

        $e_link_protocol = empty($_SERVER['HTTPS']) ? "http" : "https";
        $e_link_base = $_SERVER['SERVER_NAME'];
        $e_link_port = $_SERVER['SERVER_PORT'];
        $e_link_path = $view->url(array('user_id' => $user->getDbId(), 'token' => $token), 'password-change');

        $message = sprintf(_("Hi %s, \n\nClick this link to reset your password: "), $user->getDbLogin());
        $message .= "{$e_link_protocol}://{$e_link_base}:{$e_link_port}{$e_link_path}";

        $str = sprintf(_('%s Password Reset'), PRODUCT_NAME);
        $success = Application_Model_Email::send($str, $message, $user->getDbEmail());

        return $success;
    }

    public function invalidateTokens($user, $action)
    {
       CcSubjsTokenQuery::create()
           ->filterByDbAction($action)
           ->filterByDbUserId($user->getDbId())
           ->delete();
    }

    public function checkToken($user_id, $token, $action)
    {
        $salt = md5("pro");

        $token_info = CcSubjsTokenQuery::create()
           ->filterByDbAction($action)
           ->filterByDbUserId($user_id)
           ->filterByDbToken(sha1($token.$salt))
           ->findOne();

        if (empty($token_info)) {
            return false;
        }

        $now = new DateTime();
        $token_life = new DateInterval(self::TOKEN_LIFETIME);
        $token_created = new DateTime($token_info->getDbCreated(), new DateTimeZone("UTC"));

        return $now->sub($token_life)->getTimestamp() < $token_created->getTimestamp();
    }

    /**
     * Gets the adapter for authentication against a database table
     *
     * @return object
     */
    public static function getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('cc_subjs')
                    ->setIdentityColumn('login')
                    ->setCredentialColumn('pass')
                    ->setCredentialTreatment('MD5(?)');

        return $authAdapter;
    }

    /**
     * Get random string
     *
     * @param  int    $length
     * @param  string $allowed_chars
     * @return string
     */
    final public function generateRandomString($length = 12, $allowed_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $allowed_chars[mt_rand(0, strlen($allowed_chars) - 1)];
        }

        return $string;
    }
    
    /** It is essential to do this before interacting with Zend_Auth otherwise sessions could be shared between
     *  different copies of Airtime on the same webserver. This essentially pins this session to:
     *   - The server hostname - including subdomain so we segment multiple Airtime installs on different subdomains
     *   - The remote IP of the browser - to help prevent session hijacking
     *   - The client ID - same reason as server hostname
     * @param Zend_Auth $auth Get this with Zend_Auth::getInstance().
     */
    public static function pinSessionToClient($auth)
    {
        $CC_CONFIG = Config::getConfig();

        $serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";
        $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
        $sessionIdentifier = 'Airtime' . '-' . $serverName .  '-' . $remoteAddr .  '-' . Application_Model_Preference::GetClientId() .  '-' . $CC_CONFIG["baseDir"];
        $auth->setStorage(new Zend_Auth_Storage_Session($sessionIdentifier));
    }
}
