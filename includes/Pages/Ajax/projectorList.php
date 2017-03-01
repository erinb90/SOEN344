<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/dbc.php');
$EquipmentCatalog = new \Stark\EquipmentCatalog();

$equipment = ["data" => []];

/**
 * @var $Projector \Stark\Models\Projector
 *
 */
foreach ($EquipmentCatalog->getAllEquipment()["projectors"] as $Projector)
{


    $equipment['data'][] = [

        "EquipmentId" => $Projector->getEquipmentId(),
        "Manufacturer" => $Projector->getManufacturer(),
        "ProductLine" =>$Projector->getProductLine(),
        "Description" => $Projector->getDescription(),
        "Display" => $Projector->getDisplay(),
        "Resolution" => $Projector->getResolution()
    ];
}
echo json_encode($equipment);