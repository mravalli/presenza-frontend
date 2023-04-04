<?php


use Phinx\Seed\AbstractSeed;

class Justifications extends AbstractSeed
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
        $this->table('justifications')->insert([
            ['name' => 'Malattia', 'code' => 'M'],
            ['name' => 'Malattia Figlio', 'code' => 'MF'],
            ['name' => 'Lutto o Permesso per Grave Motivo', 'code' => 'LP'],
            ['name' => 'Maternità', 'code' => 'MT'],
            ['name' => 'Riposo', 'code' => 'R'],
            ['name' => 'Ferie', 'code' => 'F'],
            ['name' => 'Permesso Elettorale', 'code' => 'PE'],
            ['name' => 'Aspettativa Non Retribuita', 'code' => 'ANR'],
            ['name' => 'Dimissioni', 'code' => 'D'],
            ['name' => 'Cassa Integrazione', 'code' => 'CIG)'],
            ['name' => 'Congedo Parentale', 'code' => 'CP'],
            ['name' => 'Donazione Avis', 'code' => 'A'],
            ['name' => 'Permesso Studio', 'code' => 'PS'],
            ['name' => 'Congedo Matrimoniale', 'code' => 'CM'],
            ['name' => 'Infortunio / Interdizione', 'code' => 'I'],
            ['name' => 'Festività Patrono', 'code' => 'FSP'],
            ['name' => 'Permesso Allattamento', 'code' => 'AL'],
            ['name' => 'Parto', 'code' => 'PT'],
            ['name' => 'Trasferta', 'code' => 'TR'],
        ])->saveData();
    }
}
