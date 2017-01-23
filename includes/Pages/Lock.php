<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');
$roomID = $_REQUEST['roomID'];
$action = $_REQUEST['action'];

if($action == "lock")
{
    $LockMapper = new LockMapper();
    $LockDomain = $LockMapper->lockRoom($roomID, WebUser::getUser());
    UnitOfWork::registerNew($LockDomain, $LockMapper);
    if(UnitOfWork::commit())
    {

    }


}
if($action == "unlock")
{
    $LockMapper = new LockMapper();
    $LockDomain = $LockMapper->unlockRoom($roomID);
    UnitOfWork::registerDeleted($LockDomain, $LockMapper);
    if(UnitOfWork::commit())
    {

        ?>

        <?php

    }


}