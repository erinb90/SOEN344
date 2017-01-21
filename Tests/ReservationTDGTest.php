<?php

/**
 * Class ReservationTDGTest
 */
class ReservationTDGTest extends DatabaseTestCase
{
    /**
     * @covers \ReservationTDG::getReservation
     */
    public function testGetReservation()
    {
        // Arrange
        $reservationTdg = new ReservationTDG();

        // Act
        $actualReservation = $reservationTdg->getReservation(1, $this->getPdo());

        // Assert
        $expectedReservation = $this->getDataSet()->getTable("reservation")->getRow(0);

        static::assertEquals($expectedReservation, $actualReservation);
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createXMLDataSet('test_data/getReservation.xml');
    }
}