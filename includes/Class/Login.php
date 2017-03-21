<?php
namespace Stark {

    use Stark\Mappers\UserMapper;

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
        protected $_errorMessages;

        /**
         * @var PasswordHash to validate password
         */
        private $_passwordHash;

        /**
         * @var UserMapper to create user object
         */
        private $_userMapper;


        /**
         * Login constructor.
         *
         * @param array $credentials
         */
        public function __construct(array $credentials)
        {
            $this->_credentials = $credentials;
            $this->_errorMessages = [];
            $this->_passwordHash = new PasswordHash(8, FALSE);
            $this->_userMapper = new UserMapper();
        }

        /**
         * @return array of errors during login process
         */
        public function getErrors()
        {
            return $this->_errorMessages;
        }

        /**
         * Fetches an existing user using their email
         *
         * @param string $email of the user
         *
         * @return \Stark\Models\User $User or null if the user does not exist
         */
        private function fetchUser($email)
        {
            if (!isset($this->_credentials)) {
                $this->_errorMessages[] = "Empty credentials.";
                return null;
            }

            if (!isset($this->_credentials["email"])) {
                $this->_errorMessages[] = "Empty email";
                return null;
            }

            $inputPassword = $this->_credentials["password"];
            if (!isset($this->_credentials["password"])) {
                $this->_errorMessages[] = "Empty password";
                return null;
            }

            $user = $this->_userMapper->findByEmail(trim($email));

            if (!isset($user)) {
                $this->_errorMessages[] = "User does not exist.";
                return null;
            }

            $isValidPassword = $this->validatePassword($inputPassword, $user->getPassword());

            if ($isValidPassword) {
                return $user;
            }

            return null;
        }

        /**
         * Validates input password against stored user password.
         *
         * @param string $inputPassword from the login form
         * @param string $storedPassword of the user from the database
         *
         * @return boolean if input password matches stored user password
         */
        private function validatePassword($inputPassword, $storedPassword)
        {
            if (!$this->_passwordHash->CheckPassword($inputPassword, $storedPassword)) {
                $this->_errorMessages[] = "Password invalid.";
                return false;
            }

            return true;
        }

        /**
         * @return boolean returns true if successful login
         */
        public function login()
        {
            $user = $this->fetchUser($this->_credentials["email"]);
            if (empty($this->_errorMessages)) {
                session_start();
                @session_regenerate_id(true);
                $_SESSION['email'] = $user->getUserName();
                $_SESSION['sid'] = $user->getStudentId();

                return true;
            }

            return false;
        }
    }
}
