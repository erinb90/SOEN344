<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');

$ReservationProjectors = \Stark\ReservationProjectors::find($_REQUEST['id']);

$equipment = $ReservationProjectors->getProjectors();

echo json_encode($equipment);