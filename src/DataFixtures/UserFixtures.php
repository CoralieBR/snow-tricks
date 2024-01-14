<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public const USER_1_REFERENCE = 'user-1';
    public const USER_2_REFERENCE = 'user-2';
    public const USER_3_REFERENCE = 'user-3';

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setName('Léo')
            ->setEmail('leo@mail.com')
            ->setPassword('pass');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setName('Zoé')
            ->setEmail('zoe@mail.com')
            ->setPassword('pass');
        $manager->persist($user2);

        $user3 = new User();
        $user3->setName('Léa')
            ->setEmail('lea@mail.com')
            ->setPassword('pass');
        $manager->persist($user3);

        $manager->flush();

        $this->addReference(self::USER_1_REFERENCE, $user1);
        $this->addReference(self::USER_2_REFERENCE, $user2);
        $this->addReference(self::USER_3_REFERENCE, $user3);
    }
}