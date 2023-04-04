<?php declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\Clock\SystemClock;
use Neuro3\Exceptions\InvalidDataException;
use PHPUnit\Framework\TestCase;
use function Neuro3\Presenza\now;

class EmployeeUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        now(new SystemClock());
    }

    public function testCreatedAt(): void
    {
        $createdAt = new DateTimeImmutable('@0');
        now(new FrozenClock($createdAt));
        $employee = new Employee('foo', 'bar', 'SHGTQL90D19M269I');

        assertEquals($createdAt, $employee->getCreatedAt());
    }

    /**
     * @dataProvider typeSafeInvalidDataProvider
     *
     * @param string[] $invalidProperties
     */
    public function testValidation(
        $firstname,
        $lastname,
        $cf,
        $badge_id,
        $address,
        $cap,
        $city,
        $phone,
        $mobile,
        $email,
        $first_engagement,
        $birthday,
        $notes,
        array $invalidProperties
    ) {
        try {
            new Employee($firstname, $lastname, $cf, $badge_id, $address, $cap, $city, $phone, $mobile, $email, $first_engagement, $birthday, $notes);
        } catch (InvalidDataException $e) {
        }

        assertTrue(isset($e));
        assertEquals($invalidProperties, array_keys($e->getDetails()), 'Expected invalid properties don\'t match');
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param string[] $invalidProperties
     */
    public function testValidationFromArray(
        $firstname,
        $lastname,
        $cf,
        $badge_id,
        $address,
        $cap,
        $city,
        $phone,
        $mobile,
        $email,
        $first_engagement,
        $birthday,
        $notes,
        array $invalidProperties
    ) {
        try {
            Employee::createFromArray(compact(
                'badge_id',
                'firstname',
                'lastname',
                'address',
                'cap',
                'city',
                'cf',
                'phone',
                'mobile',
                'email',
                'first_engagement',
                'birthday',
                'notes'
            ));
        } catch (InvalidDataException $e) {
        }

        assertTrue(isset($e));
        assertEquals($invalidProperties, array_keys($e->getDetails()), 'Expected invalid properties don\'t match');
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param string[] $invalidProperties
     */
    public function testValidationOnUpdate(
        $firstname,
        $lastname,
        $cf,
        $badge_id,
        $address,
        $cap,
        $city,
        $phone,
        $mobile,
        $email,
        $first_engagement,
        $birthday,
        $notes,
        array $invalidProperties
    ) {
        $employee = new Employee('foo', 'bar', 'SHGTQL90D19M269I');

        try {
            $employee->updateFromArray(compact(
                'badge_id',
                'firstname',
                'lastname',
                'address',
                'cap',
                'city',
                'cf',
                'phone',
                'mobile',
                'email',
                'first_engagement',
                'birthday',
                'notes'
            ));
        } catch (InvalidDataException $e) {
        }

        assertTrue(isset($e));
        assertEquals($invalidProperties, array_keys($e->getDetails()), 'Expected invalid properties don\'t match');
    }

    /**
     * @return array[]
     */
    public function invalidDataProvider(): array
    {
        return [
            [ '', ' ', 'SHGT90D19M269I', null, null, null, null, null, null, null, null, null, null, ['firstname', 'lastname', 'cf']],
            [ '', 'bar', 'SHGTQL90D19M269I', '4K95', null, null, null, null, null, null, null, null, null, ['firstname']],
            [ 'foo', '', 'SHGTQL90D19M269I', null, null, null, null, null, null, null, 8700, null, null, ['lastname', 'first_engagement']],
            [ 'foo', 'bar', 0, null, null, null, null, null, null, 98, null, null, null, ['cf', 'email']],
            [ 'foo', '', 'foo', null, null, null, null, null, null, null, null, null, null, ['lastname', 'cf']],
        ];
    }

    /**
     * @return array[]
     */
    public function typeSafeInvalidDataProvider(): array
    {
        return [
            [ ' ', ' ', 'SHGTQL90D19M269I', null, null, null, null, null, null, null, null, null, null, ['firstname', 'lastname']],
            [ '', 'bar', 'SHGTQL90D19M269I', null, null, null, null, null, null, null, null, null, null, ['firstname']],
            [ 'foo', '', 'SHGTQL90D19M269I', null, null, null, null, null, null, null, '8700', null, null, ['lastname', 'first_engagement']],
            [ 'foo', 'bar', '', null, null, null, null, null, null, 'null@nukk@it', null, null, null, ['cf', 'email']],
            [ 'foo', '', 'foo', null, null, null, null, null, null, null, null, null, null, ['lastname', 'cf']],
        ];
    }
}
