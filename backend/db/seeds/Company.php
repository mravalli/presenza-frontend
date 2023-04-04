<?php

use Faker\Generator;
use Faker\Provider\Internet;
use Faker\Provider\it_IT\Person;
use Faker\Provider\it_IT\Address;
use Faker\Provider\it_IT\Company as CompanyFaker;
use Faker\Provider\it_IT\PhoneNumber;
use Phinx\Seed\AbstractSeed;

class Company extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $faker = new Generator();
        $faker->addProvider(new Person($faker));
        $faker->addProvider(new Address($faker));
        $faker->addProvider(new CompanyFaker($faker));
        $faker->addProvider(new Internet($faker));
        $faker->addProvider(new PhoneNumber($faker));

        $this->table('company')->insert([
            'fullname' => $faker->company,
            'vat' => $faker->vatId(),
            'cf' => $faker->taxId(),
            'address' => $faker->address,
            'cap' => $faker->postcode,
            'city' => $faker->city,
            'phone' => $faker->e164PhoneNumber,
            'email' => $faker->email,
        ])->saveData();
    }
}
