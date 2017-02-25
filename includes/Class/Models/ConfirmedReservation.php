<?php
/**
 * Created by PhpStorm.
 * User: Dimitri
 * Date: 2/25/2017
 * Time: 12:49 AM
 */

namespace Stark\Models
{


    use Stark\Interfaces\DomainObject;
    use Stark\Interfaces\Reservation;

    class ConfirmedReservation extends Reservation
    {

        public function __construct()
        {
        }

    }
}