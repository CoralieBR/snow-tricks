<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $now = new \DateTimeImmutable();

        $comment1 = new Comment();
        $comment1->setContent("J'aime vraiment beaucoup cette figure!")
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->setTrick($this->getReference(TrickFixtures::TRICK_1_REFERENCE));
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setContent("Est-ce qu'il faut prendre de l'élan pour cette figure?")
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->setTrick($this->getReference(TrickFixtures::TRICK_1_REFERENCE));
        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3->setContent("Je n'ai pas bien compris le déroulé, plus d'explications svp?")
            ->setCreatedAt($now)
            ->setUser($this->getReference(UserFixtures::USER_1_REFERENCE))
            ->setTrick($this->getReference(TrickFixtures::TRICK_1_REFERENCE));
        $manager->persist($comment3);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
            UserFixtures::class,
        ];
    }
}