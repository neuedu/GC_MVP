<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ePals\Base\User;
use ePals\UserAttribute;

class IndexController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    /**
     * return value of Cache by key
     * @param type $keyName
     * @return boolean
     */
    public function getCacheValue($keyName) {
        $cache = \Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name' => 'filesystem'
                    ),
                    'plugins' => array(
                        // Don't throw exceptions on cache errors
                        'exception_handler' => array(
                            'throw_exceptions' => false
                        ),
                    )
        ));
        $result = $cache->getItem($keyName, $success);
        if ($success) {
            return $result;
        } else {
            return false;
        }
    }

    public function setCacheItem($key, $value) {
        $cache = \Zend\Cache\StorageFactory::factory(array(
                    'adapter' => array(
                        'name' => 'filesystem'
                    ),
                    'plugins' => array(
                        // Don't throw exceptions on cache errors
                        'exception_handler' => array(
                            'throw_exceptions' => false
                        )
                    )
        ));
        $cache->setItem($key, $value);
        echo 'set';
    }

    public function homeAction() {
        session_start();
        $s = 'english.php';
        if ($_SESSION['lan']) {
            $s = $_SESSION['lan'];
        }
        include_once $s;
        return array('form' => $tra);

        $key = 'userProfile';

        $cacheInfo = $this->getCacheValue($key);

        if ($cacheInfo) {

            $viewInfo = array('message' => 'Last Visitor to our site : ' . $cacheInfo);
            return new ViewModel($viewInfo);
        } else {
            $viewInfo = array('message' => 'Welcome new user!');
            return new ViewModel($viewInfo);
        }

        return new ViewModel($viewInfo);
    }

    //identify username and password
    public function loginAction() {
        $config = new \Zend\Config\Config(require ROOT_PATH . '/config/api.php');
        $domain = $config->tenant;
        $username = $_GET['username'];
        if (!$username) {
            $request = $this->getRequest();
            $response = $this->getResponse();


            $post_data = $request->getPost();

            $username = $post_data['username'];
            $password = $post_data['password'];
            //template use: add @apals.com
            $account = "$username@$domain";
            $user = new User();
            $user->set_SIS_Server($config->sis_apiserver->url);
            $user->set_PM_Server($config->pm_apiserver->url);
            $user->setAccount($account);

            if ($user->verifyPassword($password)) {

                //save userinfo into session
                $_SESSION['username'] = $username;

                $userAttribute = new UserAttribute();
                $userAttribute->set_ElasticSearch_Server($config->elasticsearch->host . ":" . $config->elasticsearch->port);
                $userAttribute->set_SIS_Server($config->sis_apiserver->url);
                $userAttribute->loadUserAttribute($account);
                $all_attr = $userAttribute->getAll();
                $viewInfo = array('message' => 'Welcome you visit our site ! ',
                    'attr' => $all_attr);
                return new ViewModel($viewInfo);
            } else {
                $viewInfo = array('message' => 'login error');
                return new ViewModel($viewInfo);
                //跳转到错误页面
            }
        } else {
            $userAttribute = new UserAttribute();
            $userAttribute->set_ElasticSearch_Server($config->elasticsearch->host . ":" . $config->elasticsearch->port);
            $userAttribute->set_SIS_Server($config->sis_apiserver->url);
            $userAttribute->loadUserAttribute("$username@$domain");
            $all_attr = $userAttribute->getAll();
            $viewInfo = array('message' => 'Welcome you visit our site ! ',
                'attr' => $all_attr);
            return new ViewModel($viewInfo);
        }
    }

    function testAction() {
        session_start();
        $s = 'english.php';
        if ($_SESSION['lan']) {
            $s = $_SESSION['lan'];
        }

        $request = $this->getRequest();
        $response = $this->getResponse();
        $post_data = $request->getPost();
        $lan = $post_data['language'];
        if ($lan) {
            $_SESSION['lan'] = $lan;
            include_once $lan;
            return array('form' => $tra);
        } else {
            include_once $s;
            return array('form' => $tra);
        }
    }

    function logoutAction() {
        unset($_SESSION['username']);
        header("Location: http://" . $_SERVER['SERVER_NAME']);
        exit;
    }

}
