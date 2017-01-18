<?php

/**
 * Class Login
 */
class Login
{

    /**
     * @var array credentials
     */
    protected $_credentials;

    /**
     * @var array error messages
     */
    protected $_error_messages;

    /**
     * @var int user id
     */
    private $_userId;

    /**
     * Login constructor.
     *
     * @param array $credentials
     */
    public function __construct(array $credentials)
    {

        // initialize credentials
        $this->_credentials = array(
            'username' => "",
            'password' => ""
        );

        if ($credentials)
        {
            /* union of $credentials + $this->_credentials */
            $this->_credentials = $credentials + $this->_credentials;
        }
    }

    /**
     * @return bool returns true if user credentials match db credentials
     */
    private function checkUser()
    {
        $UserMapper = new UserMapper();

        $User = $UserMapper->findByUsername(trim($this->_credentials["username"]));

        if ($User)
        {
            $PasswordHash = new PasswordHash(8, FALSE); // hash the password
            $stored = $User->getPassword();
            $this->_userId = $User->getUid();
            // compare given password with stored password
            if (!$PasswordHash->CheckPassword($this->_credentials['password'], $stored))
            {
                $this->_error_messages[] = "Username or password incorrect.";
            }
        }
        else
        {
            $this->_error_messages[] = "Username or password incorrect.";
        }
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->_error_messages;
    }

    /**
     * @return bool returns true if successful login
     */
    public function login()
    {
        $this->checkUser();
        if (empty($this->_error_messages))
        {
            session_start();
            @session_regenerate_id(true);
            $_SESSION['username'] = $this->_credentials['username'];
            $_SESSION['uid'] = $this->_userId;
            return true;
        }
        return false;

    }
}

