<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Stark\CreateReservationSession;
use Stark\DeleteReservationSession;
use Stark\Mappers\ReservationMapper;
use Stark\Models\Reservation;
use Stark\RequestModels\EquipmentRequest;
use Stark\RequestModels\ReservationRequestBuilder;
use Stark\Utilities\ReservationManager;

require_once('test_settings.php');

/**
 * Class DeleteReservationTest
 * @package Tests
 */
class DeleteReservationTest extends TestCase
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

    public function testReservationIsDeleted()
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
        $reservationRequest = $reservationRequestBuilder->build();

        $createReservationSession = new CreateReservationSession($reservationRequest);
        $statusCode = $createReservationSession->reserve();

        self::assertEquals(CreateReservationSession::SUCCESS, $statusCode);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertCount(1, $reservations);
        $result = DeleteReservationSession::delete($reservationRequest->getReservationId());

        self::assertTrue($result);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertCount(0, $reservations);
    }

    public function testWaitlistedReservationsAreMadeActiveUponAvailableEquipmentAndTimes()
    {
        $allowAlternative = true;
        $this->equipmentRequests[] = new EquipmentRequest(6, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(7, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(8, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(9, 'p', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(10, 'p', $allowAlternative);

        /**
         * @var EquipmentRequest[] $equipmentRequests
         */
        $equipmentRequestsOne = [];
        $equipmentRequestsOne[] = new EquipmentRequest(6, 'c', $allowAlternative);

        /**
         * @var EquipmentRequest[] $equipmentRequests
         */
        $equipmentRequestsTwo = [];
        $equipmentRequestsTwo[] = new EquipmentRequest(9, 'p', $allowAlternative);

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation")
            ->userId("4")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 11:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestOne = $reservationRequestBuilder->build();

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 2")
            ->userId("2")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:30:00")
            ->endTimeDate("2017-04-04 11:30:00")
            ->recurrences(1)
            ->equipmentRequests($equipmentRequestsOne);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 3")
            ->userId("1")
            ->roomId("3")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 11:00:00")
            ->recurrences(1)
            ->equipmentRequests($equipmentRequestsTwo);
        $reservationRequestThree = $reservationRequestBuilder->build();

        $createReservationSessionOne = new CreateReservationSession($reservationRequestOne);
        $createReservationSessionTwo = new CreateReservationSession($reservationRequestTwo);
        $createReservationSessionThree = new CreateReservationSession($reservationRequestThree);

        self::assertEquals(CreateReservationSession::SUCCESS, $createReservationSessionOne->reserve());
        self::assertEquals(CreateReservationSession::WAITLIST, $createReservationSessionTwo->reserve());
        self::assertEquals(CreateReservationSession::WAITLIST, $createReservationSessionThree->reserve());

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertCount(3, $reservations);
        $result = DeleteReservationSession::delete($reservationRequestOne->getReservationId());

        self::assertTrue($result);

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertCount(2, $reservations);

        /**
         * @var Reservation $reservationTwo
         */
        $reservationTwo = $this->reservationMapper->findByPk($reservationRequestTwo->getReservationId());

        /**
         * @var Reservation $reservationThree
         */
        $reservationThree = $this->reservationMapper->findByPk($reservationRequestThree->getReservationId());

        self::assertFalse($reservationTwo->isIsWaited());
        self::assertFalse($reservationThree->isIsWaited());
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