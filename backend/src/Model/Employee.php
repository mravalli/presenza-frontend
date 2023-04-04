<?php
/**
 * This file is part of the project: Presenza
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 *
 * @author:    Mario Ravalli <mario@raval.li>
 * @copyright: Copyright (c) 2020 Mario Ravalli
 * @license:   GNU General Public License v3.0 or later
 *
 *
 * Creation Date: Tue Sep 08 2020
 * Modified By:   Mario Ravalli
 * Last Modified: 2021-08-05 16:57:21
 */

declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use const Neuro3\Presenza\DATE_FORMAT;
use const Neuro3\Presenza\DATETIME_FORMAT;
use function Neuro3\Presenza\date_from_string;
use function Neuro3\Presenza\datetime_from_string;
use function Neuro3\Presenza\now;
use Assert\Assert;
use Assert\LazyAssertionException;
use DateTime;
use DateTimeImmutable;
use JsonSerializable;
use Neuro3\Exceptions\InvalidDataException;

class Employee extends Model
{
    use ModelTrait;

    protected ?string $badge_id = null;
    protected string $firstname;
    protected string $lastname;
    protected ?string $address = null;
    protected ?string $cap = null;
    protected ?string $city = null;
    protected string $cf;
    protected ?string $phone = null;
    protected ?string $mobile = null;
    protected ?string $email = null;
    protected ?DateTimeImmutable $first_engagement = null;
    protected ?DateTimeImmutable $birthday = null;
    protected ?string $notes = null;

    protected ?array $offices = [];
    protected ?array $engagements = [];
    protected ?Engagement $actualEngagement = null;

    protected string $fullname;

    /**
     * @throws InvalidDataException
     */
    public function __construct(
        string $firstname,
        string $lastname,
        string $cf,
        string $badge_id = null,
        string $address = null,
        string $cap = null,
        string $city = null,
        string $phone = null,
        string $mobile = null,
        string $email = null,
        string $first_engagement = null,
        string $birthday = null,
        string $notes = null
    ) {
        $this->createdAt = now();

        $this->validate(compact(
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

        $this->badge_id    = $badge_id;
        $this->firstname   = $firstname;
        $this->lastname    = $lastname;
        $this->address     = $address;
        $this->cap         = $cap;
        $this->city        = $city;
        $this->cf          = $cf;
        $this->phone       = $phone;
        $this->mobile      = $mobile;
        $this->email       = $email;
        $this->first_engagement = ($this->first_engagement)?date_from_string($first_engagement):null;
        $this->birthday    = ($birthday)?date_from_string($birthday):null;
        $this->notes       = $notes;
        $this->fullname    = $this->getFullname();
    }

    public function getBadgeId(): string
    {
        return $this->badge_id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getCap(): ?string
    {
        return $this->cap;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCf(): string
    {
        return $this->cf;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFirstEngagement(): ?DateTimeImmutable
    {
        return $this->first_engagement;
    }

    public function getBirthday(): ?DateTimeImmutable
    {
        return $this->birthday;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getActualEngagement(DateTime $begin, DateTime $end): ?Engagement
    {
        foreach ($this->engagements as $engagement) {
            if (($begin <= $engagement->getEnd() || empty($engagement->getEnd())) && $end >= $engagement->getBegin()) {
                return $engagement;
            }
        }
        return null;
    }

    public function setActualEngagement(Engagement $actualEngagement): void
    {
        $this->actualEngagement = $actualEngagement;
    }

    public function getEngagements(): ?array
    {
        return $this->engagements;
    }

    public function setEngagements(array $engagements): void
    {
        $this->engagements = $engagements;
    }

    public function getOffices(): ?array
    {
        return $this->offices;
    }
    public function setOffices(array $offices): void
    {
        $this->offices = $offices;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->id,
            'badge_id'    => $this->badge_id,
            'firstname'   => $this->firstname,
            'lastname'    => $this->lastname,
            'address'     => $this->address,
            'cap'         => $this->cap,
            'city'        => $this->city,
            'cf'          => $this->cf,
            'phone'       => $this->phone,
            'mobile'      => $this->mobile,
            'email'       => $this->email,
            'first_engagement' => $this->first_engagement ? $this->first_engagement->format(DATE_FORMAT) : null,
            'birthday'    => $this->birthday ? $this->birthday->format(DATE_FORMAT) : null,
            'notes'       => $this->notes,
            'createdAt'   => $this->getCreatedAtAsString(),
            'fullname'    => $this->getFullname(),
            'actualEngagement' => $this->actualEngagement,
            'engagements' => $this->engagements,
            'offices'     => $this->offices,
        ];
    }

    /**
     * @param mixed[] $data
     * @throws InvalidDataException
     */
    public function updateFromArray(array $data): void
    {
        $this->validate($data);

        $this->badge_id         = $data['badge_id'] ?? $this->badge_id;
        $this->firstname        = $data['firstname'] ?? $this->firstname;
        $this->lastname         = $data['lastname'] ?? $this->lastname;
        $this->address          = $data['address'] ?? $this->address;
        $this->cap              = $data['cap'] ?? $this->cap;
        $this->city             = $data['city'] ?? $this->city;
        $this->cf               = $data['cf'] ?? $this->cf;
        $this->phone            = $data['phone'] ?? $this->phone;
        $this->mobile           = $data['mobile'] ?? $this->mobile;
        $this->email            = $data['email'] ?? $this->email;
        $this->first_engagement = $data['first_engagement'] ? date_from_string($data['first_engagement']) : $this->first_engagement;
        $this->birthday         = $data['birthday'] ? date_from_string($data['birthday']) : $this->birthday;
        $this->notes            = $data['notes'] ?? $this->notes;
        $this->fullname         = $this->getFullname();
    }

    /**
     * Validate Employee Data
     * @param  array  $data
     * @throws InvalidDataException
     * TODO: validate missing for birthday and first_engagement
     */
    private function validate(array $data): void
    {
        $assert = Assert::lazy()->tryAll();

        if (isset($data['badge_id'])) {
            $assert->that($data['badge_id'], 'badge_id')->nullOr()->string()->notBlank()->regex('/[\sa-zA-Z0-9]{1,15}/');
        }
        if (isset($data['firstname'])) {
            $assert->that($data['firstname'], 'firstname')->notEmpty()->string()->notBlank()->regex('/[\sa-zA-Z]{3,64}/');
        }
        if (isset($data['lastname'])) {
            $assert->that($data['lastname'], 'lastname')->notEmpty()->string()->notBlank()->regex('/[\sa-zA-Z]{3,64}/');
        }
        if (isset($data['address'])) {
            $assert->that($data['address'], 'address')->nullOr()->string()->notBlank()->regex('/\s*([a-zA-Z.]+\s*[a-zA-Z],?+\s)*[0-9]*/');
        }
        if (isset($data['cap'])) {
            $assert->that($data['cap'], 'cap')->nullOr()->string()->notBlank()->regex('/\d{5}/');
        }
        if (isset($data['city'])) {
            $assert->that($data['city'], 'city')->nullOr()->string()->notBlank()->regex('/^[a-zA-Z]+(\s[a-zA-Z]+)*$/');
        }
        if (isset($data['cf'])) {
            $assert->that($data['cf'], 'cf')->nullOr()->string()->notBlank()->regex('/^(?:[a-zA-Z][aeiouAEIOU][aeiouxAEIOUX]|[b-df-hj-np-tv-zB-DF-HJ-NP-TV-Z]{2}[a-zA-Z]){2}(?:[\dlmnp-vLMNP-V]{2}(?:[a-ehlmpr-tA-EHLMPR-T](?:[04lqLQ][1-9mnp-vMNP-V]|[15mrMR][\dmlnp-vLMNP-V]|[26nsNS][0-8lmnp-uLMNP-U])|[dhpsDHPS][37ptPT][0lL]|[acelmrtACELMRT][37ptPT][01lmLM]|[ac-ehlmlpr-tAC-EHLMPR-T][26nsNS][9vV])|(?:[02468lnqsuLNQSU][048lquLQU]|[13579mprtvMPRTV][26nsNS])bB[26nsNS][9vV])(?:[a-mzA-MZ][1-9mnp-vMNP-V][\dlmnp-vLMNP-V]{2}|[a-mA-M][0lL](?:[1-9mnp-vMNP-V][\dlmnp-vLMNP-V]|[0lL][1-9mnp-vMNP-V]))[a-zA-Z]$/');
        }
        if (isset($data['phone'])) {
            $assert->that($data['phone'], 'phone')->nullOr()->string()->notBlank()->regex('/((\+|00)?\d{2,3})?\s?\d{1,4}\s?(\d{3}\s(\d{3,4}|(\d{2}\s\d{2}))|\d{2}\s?\d{2}\s?\d{2,3}|\d{5,7})$/');
        }
        if (isset($data['mobile'])) {
            $assert->that($data['mobile'], 'mobile')->nullOr()->string()->notBlank()->regex('/((\+|00)?\d{2,3})?\s?\d{1,4}\s?(\d{3}\s(\d{3,4}|(\d{2}\s\d{2}))|\d{2}\s?\d{2}\s?\d{2,3}|\d{5,7})$/');
        }
        if (isset($data['email'])) {
            $assert->that($data['email'], 'email')->nullOr()->email();
        }
        if (isset($data['first_engagement'])) {
            $assert->that($data['first_engagement'], 'first_engagement')->nullOr()->date(DATE_FORMAT);
        }
        if (isset($data['birthday'])) {
            $assert->that($data['birthday'], 'birthday')->nullOr()->date(DATE_FORMAT);
        }

        try {
            $assert->verifyNow();
        } catch (LazyAssertionException $lae) {
            throw InvalidDataException::fromLazyAssertionException($lae);
        }
    }
}
