<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-20
 * Time: 1:53 PM
 */

$Login = new Stark\Login($_POST);

$Login->login();


?>