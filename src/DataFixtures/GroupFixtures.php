<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Group;

class GroupFixtures extends Fixture
{
    public const GROUP_1_REFERENCE = 'group-1';
    public const GROUP_2_REFERENCE = 'group-2';
    public const GROUP_3_REFERENCE = 'group-3';

    public function load(ObjectManager $manager)
    {
        $group1 = new Group();
        $group1->setName('grab ')
            ->setDescription("Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab signifie « attraper. » Il existe plusieurs types de grabs selon la position de la saisie et la main choisie pour l'effectuer, avec des difficultés variables.");
        $manager->persist($group1);

        $group2 = new Group();
        $group2->setName('rotations')
            ->setDescription("On désigne par le mot « rotation » uniquement des rotations horizontales ; les rotations verticales sont des flips. Le principe est d'effectuer une rotation horizontale pendant le saut, puis d'attérir en position switch ou normal. La nomenclature se base sur le nombre de degrés de rotation effectués.");
        $manager->persist($group2);

        $group3 = new Group();
        $group3->setName('flips')
            ->setDescription("Un flip est une rotation verticale. On distingue les front flips, rotations en avant, et les back flips, rotations en arrière. Il est possible de faire plusieurs flips à la suite, et d'ajouter un grab à la rotation. Les flips agrémentés d'une vrille existent aussi (Mac Twist, Hakon Flip...), mais de manière beaucoup plus rare, et se confondent souvent avec certaines rotations horizontales désaxées. Néanmoins, en dépit de la difficulté technique relative d'une telle figure, le danger de retomber sur la tête ou la nuque est réel et conduit certaines stations de ski à interdire de telles figures dans ses snowparks.");
        $manager->persist($group3);

        $manager->flush();

        $this->addReference(self::GROUP_1_REFERENCE, $group1);
        $this->addReference(self::GROUP_2_REFERENCE, $group2);
        $this->addReference(self::GROUP_3_REFERENCE, $group3);
    }
}