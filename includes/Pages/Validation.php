<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

/**
 * Created by PhpStorm.
 * User: dimitri
 * Date: 2017-01-20
 * Time: 1:53 PM
 */

$Login = new Stark\Login($_POST);


if ($Login->login())
{


    ?>
    <script>window.location.replace("includes/Pages/Home.php");</script>

    <?php


}
else
{
    $errors = $Login->getErrors();
    ?>
    <script>
        $(function ()
        {
            // $('#form')[0].reset();
        });
    </script>
    <br>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php
        $msg .= "<ul>";
        foreach ($errors as $error)
        {
            $msg .= '<li>' . $error . '</li>';
        }
        $msg .= "</ul>";
        echo $msg;
        ?>
    </div>
    <?php


}
?>