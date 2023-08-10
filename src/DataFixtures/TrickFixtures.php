<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Trick;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public const TRICK_1_REFERENCE = 'trick-1';
    public const TRICK_2_REFERENCE = 'trick-2';
    public const TRICK_3_REFERENCE = 'trick-3';

    public function load(ObjectManager $manager)
    {
        $now = new \DateTimeImmutable();

        $trick1 = new Trick();
        $trick1->setName('mute ')
            ->setSlug('mute')
            ->setDescription('saisie de la carre frontside de la planche entre les deux pieds avec la main avant')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_1_REFERENCE));
        $manager->persist($trick1);

        $trick2 = new Trick();
        $trick2->setName('indy')
            ->setSlug('indy')
            ->setDescription('saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_1_REFERENCE));
        $manager->persist($trick2);

        $trick3 = new Trick();
        $trick3->setName('stalefish')
            ->setSlug('stalefish')
            ->setDescription('saisie de la carre backside de la planche entre les deux pieds avec la main arrière')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_1_REFERENCE));
        $manager->persist($trick3);

        $trick4 = new Trick();
        $trick4->setName('tail grab')
            ->setSlug('tail-grab')
            ->setDescription('saisie de la partie arrière de la planche, avec la main arrière')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_1_REFERENCE));
        $manager->persist($trick4);

        $trick5 = new Trick();
        $trick5->setName('truck driver')
            ->setSlug('truck-driver')
            ->setDescription('saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_1_REFERENCE));
        $manager->persist($trick5);

        $trick6 = new Trick();
        $trick6->setName('360')
            ->setSlug('360')
            ->setDescription('trois six pour un tour complet ')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_2_REFERENCE));
        $manager->persist($trick6);

        $trick7 = new Trick();
        $trick7->setName('540')
            ->setSlug('540')
            ->setDescription('cinq quatre pour un tour et demi')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_2_REFERENCE));
        $manager->persist($trick7);

        $trick8 = new Trick();
        $trick8->setName('900')
            ->setSlug('900')
            ->setDescription('deux tours et demi')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_2_REFERENCE));
        $manager->persist($trick8);

        $trick9 = new Trick();
        $trick9->setName('seat belt')
            ->setSlug('seat-belt')
            ->setDescription('saisie du carre frontside à l\'arrière avec la main avant')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_1_REFERENCE));
        $manager->persist($trick9);

        $trick10 = new Trick();
        $trick10->setName('back flip')
            ->setSlug('back-flip')
            ->setDescription('rotations en arrière')
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->addGroup($this->getReference(GroupFixtures::GROUP_3_REFERENCE));
        $manager->persist($trick10);

        $manager->flush();

        $this->addReference(self::TRICK_1_REFERENCE, $trick1);
        $this->addReference(self::TRICK_2_REFERENCE, $trick2);
        $this->addReference(self::TRICK_3_REFERENCE, $trick3);
    }

    public function getDependencies()
    {
        return [
            GroupFixtures::class,
            UserFixtures::class,
        ];
    }
}