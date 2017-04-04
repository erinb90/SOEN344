<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Stark\CreateReservationSession;
use Stark\Mappers\ReservationMapper;
use Stark\RequestModels\EquipmentRequest;
use Stark\Models\Reservation;
use Stark\RequestModels\ReservationRequest;
use Stark\RequestModels\ReservationRequestBuilder;
use Stark\Utilities\ReservationManager;

require_once ('test_settings.php');

/**
 * Class CreateReservationTest
 * @package Tests
 */
class CreateReservationTest extends TestCase
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

    public function testCreateValidReservationNoEquipmentIsSuccess()
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
        $errors = $createReservationSession->getErrors();
        $waitListPosition = $createReservationSession->getWaitListPosition();

        self::assertEquals(CreateReservationSession::SUCCESS, $statusCode);
        self::assertEmpty($errors);
        self::assertEquals(ReservationRequest::NO_RESERVATION_ID, $waitListPosition);

        /**
         * @var Reservation $reservation
         */
        $reservation = $this->reservationMapper->findByPk($reservationRequest->getReservationId());
        self::assertNotNull($reservation);
    }

    public function testCreateValidReservationWithEquipmentIsSuccess()
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
        $reservationRequest = $reservationRequestBuilder->build();

        $createReservationSession = new CreateReservationSession($reservationRequest);
        $statusCode = $createReservationSession->reserve();
        $errors = $createReservationSession->getErrors();
        $waitListPosition = $createReservationSession->getWaitListPosition();

        /**
         * @var Reservation $reservation
         */
        $reservation = $this->reservationMapper->findByPk($reservationRequest->getReservationId());

        self::assertEquals(CreateReservationSession::SUCCESS, $statusCode);
        self::assertEmpty($errors);
        self::assertEquals(ReservationRequest::NO_RESERVATION_ID, $waitListPosition);
        self::assertNotNull($reservation);

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

    public function testReservationWithTimeConflictIsWaitlisted()
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

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 2")
            ->userId("2")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:30:00")
            ->endTimeDate("2017-04-04 11:30:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $createReservationSessionOne = new CreateReservationSession($reservationRequestOne);
        $createReservationSessionOne->reserve();

        $createReservationSessionTwo = new CreateReservationSession($reservationRequestTwo);
        $statusCode = $createReservationSessionTwo->reserve();
        $errors = $createReservationSessionTwo->getErrors();
        $waitListPosition = $createReservationSessionTwo->getWaitListPosition();

        /**
         * @var Reservation $reservation
         */
        $reservation = $this->reservationMapper->findByPk($reservationRequestTwo->getReservationId());

        self::assertEquals(CreateReservationSession::WAITLIST, $statusCode);
        self::assertCount(1, $errors);
        self::assertEquals(1, $waitListPosition);
        self::assertNotNull($reservation);
    }

    public function testReservationWithEquipmentConflictIsWaitlisted()
    {
        $allowAlternative = false;
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

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 2")
            ->userId("2")
            ->roomId("2")
            ->startTimeDate("2017-04-04 10:30:00")
            ->endTimeDate("2017-04-04 11:30:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $createReservationSessionOne = new CreateReservationSession($reservationRequestOne);
        $createReservationSessionOne->reserve();

        $createReservationSessionTwo = new CreateReservationSession($reservationRequestTwo);
        $statusCode = $createReservationSessionTwo->reserve();
        $errors = $createReservationSessionTwo->getErrors();
        $waitListPosition = $createReservationSessionTwo->getWaitListPosition();

        /**
         * @var Reservation $reservation
         */
        $reservation = $this->reservationMapper->findByPk($reservationRequestTwo->getReservationId());

        self::assertEquals(CreateReservationSession::WAITLIST, $statusCode);
        self::assertCount(10, $errors);
        self::assertEquals(1, $waitListPosition);
        self::assertNotNull($reservation);
    }

    public function testCapstoneStudentHasPriorityWaitlistPosition()
    {
        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation")
            ->userId("2")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 11:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestOne = $reservationRequestBuilder->build();

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 2")
            ->userId("4")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:30:00")
            ->endTimeDate("2017-04-04 11:30:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 3")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:30:00")
            ->endTimeDate("2017-04-04 11:30:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestThree = $reservationRequestBuilder->build();

        $createReservationSessionOne = new CreateReservationSession($reservationRequestOne);
        $createReservationSessionOne->reserve();

        $createReservationSessionTwo = new CreateReservationSession($reservationRequestTwo);
        $createReservationSessionTwo->reserve();
        $waitListPositionNonCapstone = $createReservationSessionTwo->getWaitListPosition();

        self::assertEquals(1, $waitListPositionNonCapstone);

        $createReservationSessionThree = new CreateReservationSession($reservationRequestThree);
        $statusCode = $createReservationSessionThree->reserve();
        $errors = $createReservationSessionThree->getErrors();
        $waitListPositionCapstone = $createReservationSessionThree->getWaitListPosition();

        /**
         * @var Reservation $reservation
         */
        $reservationCapstone = $this->reservationMapper->findByPk($reservationRequestThree->getReservationId());

        self::assertEquals(CreateReservationSession::WAITLIST, $statusCode);
        self::assertCount(1, $errors);
        self::assertNotNull($reservationCapstone);

        $waitListPositionNonCapstone = $this->reservationManager->getWaitListPosition($reservationRequestTwo->getReservationId());

        self::assertEquals(1, $waitListPositionCapstone);
        self::assertEquals(2, $waitListPositionNonCapstone);
    }

    public function testReservationEquipmentConflictWithAllowAlternativeIsSuccess()
    {
        $allowAlternative = true;
        $this->equipmentRequests[] = new EquipmentRequest(6, 'c', $allowAlternative);
        $this->equipmentRequests[] = new EquipmentRequest(7, 'c', $allowAlternative);

        /**
         * @var EquipmentRequest[] $equipmentRequests
         */
        $equipmentRequests = [];
        $equipmentRequests[] = new EquipmentRequest(6, 'c', $allowAlternative);

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

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 2")
            ->userId("2")
            ->roomId("2")
            ->startTimeDate("2017-04-04 10:30:00")
            ->endTimeDate("2017-04-04 11:30:00")
            ->recurrences(1)
            ->equipmentRequests($equipmentRequests);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $createReservationSessionOne = new CreateReservationSession($reservationRequestOne);
        $createReservationSessionOne->reserve();

        $createReservationSessionTwo = new CreateReservationSession($reservationRequestTwo);
        $statusCode = $createReservationSessionTwo->reserve();
        $errors = $createReservationSessionTwo->getErrors();
        $waitListPosition = $createReservationSessionTwo->getWaitListPosition();

        /**
         * @var Reservation $reservation
         */
        $reservation = $this->reservationMapper->findByPk($reservationRequestTwo->getReservationId());

        self::assertEquals(CreateReservationSession::SUCCESS, $statusCode);
        self::assertCount(1, $errors);
        self::assertEquals(ReservationRequest::NO_RESERVATION_ID, $waitListPosition);
        self::assertNotNull($reservation);

        $loanedEquipments = $this->reservationManager->getLoanedEquipmentForReservation($reservation->getReservationID());
        self::assertCount(1, $loanedEquipments);
        $equipmentIds = [];
        foreach ($loanedEquipments as $loanedEquipment) {
            $equipmentIds[] = $loanedEquipment->getEquipmentId();
        }

        self::assertContains(8, $equipmentIds);

    }

    public function testUserSurpassMaxBookingTimeForWeekFails()
    {
        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 12:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestOne = $reservationRequestBuilder->build();

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 2")
            ->userId("1")
            ->roomId("2")
            ->startTimeDate("2017-04-04 13:00:00")
            ->endTimeDate("2017-04-04 15:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $createReservationSessionOne = new CreateReservationSession($reservationRequestOne);
        $createReservationSessionOne->reserve();

        $createReservationSessionTwo = new CreateReservationSession($reservationRequestTwo);
        $statusCode = $createReservationSessionTwo->reserve();
        $errors = $createReservationSessionTwo->getErrors();

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertEquals(CreateReservationSession::ERROR, $statusCode);
        self::assertCount(1, $errors);
        self::assertCount(1, $reservations);
    }

    public function testUserMaxBookingTimeSeparateWeeksIsSuccess()
    {
        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 13:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestOne = $reservationRequestBuilder->build();

        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation 2")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate("2017-04-20 10:00:00")
            ->endTimeDate("2017-04-20 13:00:00")
            ->recurrences(1)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequestTwo = $reservationRequestBuilder->build();

        $createReservationSessionOne = new CreateReservationSession($reservationRequestOne);
        $createReservationSessionOne->reserve();

        $createReservationSessionTwo = new CreateReservationSession($reservationRequestTwo);
        $statusCode = $createReservationSessionTwo->reserve();
        $errors = $createReservationSessionTwo->getErrors();

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertEquals(CreateReservationSession::SUCCESS, $statusCode);
        self::assertEmpty($errors);
        self::assertCount(2, $reservations);
    }

    public function testUserSurpassMaxRecurrencesFails()
    {
        $reservationRequestBuilder = new ReservationRequestBuilder();
        $reservationRequestBuilder
            ->title("Test Reservation")
            ->userId("1")
            ->roomId("1")
            ->startTimeDate("2017-04-04 10:00:00")
            ->endTimeDate("2017-04-04 12:00:00")
            ->recurrences(4)
            ->equipmentRequests($this->equipmentRequests);
        $reservationRequest = $reservationRequestBuilder->build();

        $createReservationSession = new CreateReservationSession($reservationRequest);
        $statusCode = $createReservationSession->reserve();
        $errors = $createReservationSession->getErrors();

        /**
         * @var Reservation[] $reservation
         */
        $reservations = $this->reservationMapper->getReservations();

        self::assertEquals(CreateReservationSession::ERROR, $statusCode);
        self::assertCount(1, $errors);
        self::assertEmpty($reservations);
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