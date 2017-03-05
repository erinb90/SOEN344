<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

/**
 * @var $ReservationComputers \Stark\ReservationComputers
 */
$ReservationComputers = \Stark\ReservationComputers::find($_REQUEST['id']);

$equipment = $ReservationComputers->getComputers();

echo json_encode($equipment);