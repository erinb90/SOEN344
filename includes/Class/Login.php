<?php
namespace Stark;
use Stark\PasswordHash;
use Stark\Mappers\StudentMapper;


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
    private $_studentID;

    /**
     * Login constructor.
     *
     * @param array $credentials
     */
    public function __construct(array $credentials)
    {

        // initialize credentials
        $this->_credentials = [
            'email' => "",
            'password' => ""
        ];

        if ($credentials)
        {
            /* union of $credentials + $this->_credentials */
            $this->_credentials = $credentials + $this->_credentials;
        }
    }

    /**
     * @return bool returns true if user credentials match db credentials
     */
    public function checkUser()
    {
        $UserMapper = new StudentMapper();

        /**
         * @var \Stark\Models\StudentDomain $User
         */
        $User = $UserMapper->findByEmail(trim($this->_credentials["email"]));

        if ($User)
        {
            $PasswordHash = new PasswordHash(8, FALSE); // hash the password
            $stored = $User->getPassword();
            $this->_studentID = $User->getSID();
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
            $_SESSION['email'] = $this->_credentials['email'];
            $_SESSION['sid'] = $this->_studentID;
            return true;
        }
        return false;

    }
}

