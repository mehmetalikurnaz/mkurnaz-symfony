<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use App\Entity\AboutMe;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AboutMeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            ['key' => 'name', 'value' => 'Mehmet Ali'],
            ['key' => 'description', 'value' => 'Text area. Text area. Text area.'],
        ];

        foreach ($data as $item) {
            $aboutMe = (new AboutMe())
                ->setKey($item['key'])
                ->setValue($item['value']);

            $manager->persist($aboutMe);
        }

        $manager->flush();
    }
}
