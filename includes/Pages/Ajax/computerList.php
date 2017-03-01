<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');
$EquipmentCatalog = new \Stark\EquipmentCatalog();

$equipment = ["data" => []];

/**
 * @var $Computer \Stark\Models\Computer
 *
 */
foreach ($EquipmentCatalog->getAllEquipment()["computers"] as $Computer)
{


    $equipment['data'][] = [

        "EquipmentId" => $Computer->getEquipmentId(),
        "Manufacturer" => $Computer->getManufacturer(),
        "ProductLine" =>$Computer->getProductLine(),
        "Description" => $Computer->getDescription(),
        "Ram" => $Computer->getRam(),
        "Cpu" => $Computer->getCpu()
    ];
}
echo json_encode($equipment);