<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');
$RoomRegistry = new \Stark\RoomDirectory();
$rooms = $RoomRegistry->getRooms();
function getColor()
{
    $a = array (
        "cyan"
    );
    return $a[array_rand($a)];
}
$output = array();
/**
 * @var $Room \Stark\Models\Room
 */
foreach($rooms as $Room)
{
    $output[] = array(
        "id"=> $Room->getRoomId(),
        "title" => $Room->getName(),
        "color" => $Room->getColor()
    );
}
echo json_encode($output);