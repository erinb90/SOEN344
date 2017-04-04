<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Stark\CreateReservationSession;
use Stark\DeleteReservationSession;
use Stark\Mappers\ReservationMapper;
use Stark\Models\Reservation;
use Stark\ModifyReservationSession;
use Stark\RequestModels\EquipmentRequest;
use Stark\RequestModels\ReservationRequestBuilder;
use Stark\Utilities\ReservationManager;

require_once('test_settings.php');

/**
 * Class ModifyReservationTest
 * @package Tests
 */
class ModifyReservationTest extends TestCase
{
    /**
     * @var EquipmentRequest[] $equipmentRequests
     */
    private $equipmentRequests;

    /**
     * @var ReservationMapper $reservationMapper
     */
    private $reservationMapper;

    /**
     * @var ReservationManager $reservationManager ;
     */
    private $reservationManager;

    public function setUp()
    {
        $this->equipmentRequests = [];
        $this->reservationMapper = new ReservationMapper();
        $this->reservationManager = new ReservationManager();
    }

    public function testReservationModificationIsSuccess()
    {
        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 11:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestOne = $reservationRequestBuilder->build();

        $createReservationSession = new CreateReservationSession($reservationRequestOne);
        $statusCode = $createReservationSession->reserve();

        $allowAlternative = true;
        $this->equipmentRequests[] = new EquipmentRequest(6, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(7, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(8, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(9, 'p', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(10, 'p', $allowAlternative);

        self::assertEquals(CreateReservationSession::SUCCESS, $statusCode);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertCount(1, $reservations);

        $newStartTimeDate = "2017-04-04 10:00:00";
        $newEndTimeDate = "2017-04-04 13:00:00";

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->reservationId($reservationRequestOne->getReservationId())
            ->title("Test Reservation")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate($newStartTimeDate)
            ->endTimeDate($newEndTimeDate)
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $modifyReservationSession = new ModifyReservationSession();
        $modifyReservationSession->modify(true, $reservationRequestTwo);
        $errors = $modifyReservationSession->getErrors();

        self::assertEmpty($errors);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();
        /**
         * @var Reservation $reservation
         */
        $reservation = $reservations[0];
        self::assertCount(1, $reservations);
        self::assertEquals($reservation->getStartTimeDate(), $newStartTimeDate);
        self::assertEquals($reservation->getEndTimeDate(), $newEndTimeDate);

        $loanedEquipments = $this->reservationManager->getLoanedEquipmentForReservation($reservation->getReservationID());
        self::assertCount(5, $loanedEquipments);
        $equipmentIds = [];
        foreach ($loanedEquipments as $loanedEquipment) {
            $equipmentIds[] = $loanedEquipment->getEquipmentId();
        }

        self::assertContains(6, $equipmentIds);
        self::assertContains(7, $equipmentIds);
        self::assertContains(8, $equipmentIds);
        self::assertContains(9, $equipmentIds);
        self::assertContains(10, $equipmentIds);
    }

    public function testReservationModificationCausesTimeConflict()
    {
        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 11:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestOne = $reservationRequestBuilder->build();

        $createReservationSession = new CreateReservationSession($reservationRequestOne);
        $statusCode = $createReservationSession->reserve();

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 3")
            ->userId("2")
            ->roomId("1")
            ->startTimeDate("2017-04-04 12:00:00")
            ->endTimeDate("2017-04-04 15:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestThree = $reservationRequestBuilder->build();

        $createReservationSessionTwo = new CreateReservationSession($reservationRequestThree);
        $createReservationSessionTwo->reserve();

        $allowAlternative = true;
        $this->equipmentRequests[] = new EquipmentRequest(6, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(7, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(8, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(9, 'p', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(10, 'p', $allowAlternative);

        self::assertEquals(CreateReservationSession::SUCCESS, $statusCode);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertCount(2, $reservations);

        $newStartTimeDate = "2017-04-04 10:00:00";
        $newEndTimeDate = "2017-04-04 13:00:00";

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->reservationId($reservationRequestOne->getReservationId())
            ->title("Test Reservation 2")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate($newStartTimeDate)
            ->endTimeDate($newEndTimeDate)
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $modifyReservationSession = new ModifyReservationSession();
        $modifyReservationSession->modify(true, $reservationRequestTwo);
        $errors = $modifyReservationSession->getErrors();

        self::assertCount(1, $errors);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();
        /**
         * @var Reservation $reservation
         */

        self::assertCount(2, $reservations);
    }

    public function testReservationModificationFreesEquipmentForWaitlistedReservation()
    {
        $allowAlternative = true;
        $this->equipmentRequests[] = new EquipmentRequest(6, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(7, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(8, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(9, 'p', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(10, 'p', $allowAlternative);

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 11:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestOne = $reservationRequestBuilder->build();

        $createReservationSession = new CreateReservationSession($reservationRequestOne);
        $createReservationSession->reserve();

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 3")
            ->userId("2")
            ->roomId("2")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 11:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestThree = $reservationRequestBuilder->build();

        $createReservationSessionTwo = new CreateReservationSession($reservationRequestThree);
        $statusCode = $createReservationSessionTwo->reserve();

        self::assertEquals(CreateReservationSession::WAITLIST, $statusCode);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertCount(2, $reservations);

        $newStartTimeDate = "2017-04-04 10:00:00";
        $newEndTimeDate = "2017-04-04 13:00:00";

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->reservationId($reservationRequestOne->getReservationId())
            ->title("Test Reservation 2")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate($newStartTimeDate)
            ->endTimeDate($newEndTimeDate)
            ->recurrences(1)
            ->equipmentRequests([]);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $modifyReservationSession = new ModifyReservationSession();
        $modifyReservationSession->modify(true, $reservationRequestTwo);
        $errors = $modifyReservationSession->getErrors();

        self::assertCount(0, $errors);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();
        /**
         * @var Reservation $reservation
         */

        self::assertCount(2, $reservations);

        /**
         * @var Reservation $newActiveReservation
         */
        $newActiveReservation = $this->reservationMapper->findByPk($reservationRequestThree->getReservationId());

        self::assertFalse($newActiveReservation->isIsWaited());
    }

    public function tearDown()
    {
        // Delete reservations
        /**
         * @var Reservation[] $reservations
         */
        $reservations = $this->reservationMapper->getReservations();
        foreach ($reservations as $reservation) {
            $this->reservationMapper->delete($reservation);
        }
    }
}