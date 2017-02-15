<?php
namespace Stark;
use Stark\Models\StudentDomain;

/**
 * Class WebUser
 *
 * This class simply holds the current user object that has logged into the system.
 * Created as a singleton in order to be able to access object easily across application.
 */
class WebUser
{

    /**
     * @var \Stark\Models\StudentDomain
     */
    static private $_User;

    /**
     * WebUser constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param \Stark\Models\StudentDomain $user
     */
    public static function setUser(StudentDomain $user)
    {
        self::$_User = $user;
    }


    /**
     * @return \Stark\Models\StudentDomain returns the user
     */
    public static function getUser()
    {
        return self::$_User;
    }

    /**
     * @param bool $redirect if redirect is set to true, the user will be redirected to root index page. To be used on HTML pages.
     *
     * @return bool returns true if the user is still logged in
     */
    public static function isLoggedIn($redirect = false)
    {
        $root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        $loggegIn = (!empty($_SESSION) && isset($_SESSION['uid']));
        if ($loggegIn)
        {
            return true;
        }
        else
        {
            if ($redirect)
            {
                header("location: " . $root . '?l=0&u=' . time());

            }

            return false;
        }

    }
}