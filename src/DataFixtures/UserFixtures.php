<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('mkurnaz@edu.cdv.pl');
        $user->setPassword('mkurnaz123');

        $manager->persist($user);
        $manager->flush();
    }
}
