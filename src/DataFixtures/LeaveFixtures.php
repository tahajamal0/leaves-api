<?php

namespace App\DataFixtures;

use App\Factory\LeaveFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LeaveFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        LeaveFactory::createMany(5);
    }
}
