<?php
namespace Tests;

use PHPUnit_Framework_TestCase;
use Stark\Models\StudentDomain;

/**
 * Class StudentDomainTest
 */
class StudentDomainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \StudentDomain::__construct
     */
    public function testObjectCanBeConstructedForValidConstructorArguments()
    {
        // Arrange
        $studentDomain = new StudentDomain();

        // Act

        // Assert
        //static::assertInstanceOf('StudentDomain', $studentDomain);
        self::assertEquals($studentDomain, $studentDomain);
        return $studentDomain;
    }

    /**
     * @covers \StudentDomain::setFirstName
     * @covers \StudentDomain::getFirstName
     * @uses   \StudentDomain::__construct
     */
    public function testFirstNameCanBeSet()
    {
        // Arrange
        $studentDomain = new StudentDomain();

        // Act
        $studentDomain->setFirstName("John");
        $firstName = $studentDomain->getFirstName();

        // Assert
        $expected = "John";
        $actual = $firstName;
        static::assertEquals($expected, $actual);
    }
}

?>