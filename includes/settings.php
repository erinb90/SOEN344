<?php
return array(
    'appname'       => 'BookIT',
    'protocol'      => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://",
    'timezone'      => 'America/Montreal',
    'db' => array(
        'development' => 'mysql://username:password@host/MY_DB',
        'production'  => 'mysql://username:password@host/MY_DB',
    ),
    'reservations' => array(
        'max_per_week' => 3,    // hours
        'max_per_reservation' => 180  // mins
    )

);
?>