<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');
$roomID = $_REQUEST['roomID'];
$action = $_REQUEST['action'];

use Stark\Mappers\LockMapper;
use Stark\UnitOfWork;
use Stark\WebUser;

//todo: this should all be moved to a class

$LockMapper = new LockMapper();
$response["error"] = null;
$response["success"] = false;

if ($action == "lock")
{

    try
    {
        /**
         * @var $RoomLock \Stark\Models\LockDomain
         */
        $RoomLock = $LockMapper->getRoomLockByRoomId($roomID);

        $startTime = date("Y-m-d H:i:s");
        $endTime = date('Y-m-d H:i:s',strtotime('+'. \Stark\CoreConfig::settings()['reservations']['lock'].' seconds',strtotime($startTime)));


        if($RoomLock)
        {
            //Expired lock? Delete old one and create a fresh one. This might have happened if browser refreshed or closed.
            if($RoomLock->isExpired())
            {

                $LockMapper->uowDelete($RoomLock);

                $LockDomain = $LockMapper->lockRoom($roomID, $startTime, $endTime, WebUser::getUser());
                $LockMapper->uowInsert($LockDomain);
                $LockMapper->commit();

                $response["success"] = true;
            }
            else if($RoomLock->getUserId() != WebUser::getUser()->getUserId())
            {
                $response["error"] = "This room is locked and will be released on " . $RoomLock->getLockEndTime();
            }
            else
            {
                $response["success"] = true;
                $response["remaining"] =  $RoomLock->getRemainingSeconds();
            }

        }
        else
        {

            $LockDomain = $LockMapper->lockRoom($roomID, $startTime, $endTime, WebUser::getUser());
            $LockMapper->uowInsert($LockDomain);
            $LockMapper->commit();
            $response["success"] = true;

        }
    }
    catch(Exception $e)
    {
        $response["error"] = $e->getMessage();
    }


    echo json_encode($response);


}
if ($action == "unlock")
{
    $response["success"] = false;
    $response["error"] =  null;
    $response["secondsDefault"]  = \Stark\CoreConfig::settings()['reservations']['lock'];
    try
    {
        /**
         * @var $RoomLock \Stark\Models\LockDomain
         */
        $LockDomain = $LockMapper->getRoomLockByRoomId($roomID);
        if($LockDomain)
        {
            $LockMapper->uowDelete($LockDomain);
            $LockMapper->commit();
        }


        $response["success"] = true;

    }
    catch(Exception $e)
    {
        $response["error"] = $e->getMessage();
    }

    echo json_encode($response);

}