<?php

namespace matuck\MenuBundle\DataFixtures\ORM;
use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Doctrine\Common\Persistence\ObjectManager;
use matuck\MenuBundle\Entity\MenuEntry;


class LoadMenusData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load Some Test Menus
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        //Create a mainmenu
        $rootmain = new MenuEntry();
        $rootmain->setTitle("main");
        $manager->persist($rootmain);

        $home = new MenuEntry();
        $home->setTitle('Home');
        $home->setParent($rootmain);
        $home->setUrl('/');
        $manager->persist($home);

        $dropdown = new MenuEntry();
        $dropdown->setTitle('dropdown');
        $dropdown->setParent($rootmain);
        $dropdown->setUrl('/dropdown');
        $manager->persist($dropdown);

        $sub1 = new MenuEntry();
        $sub1->setTitle('sub1');
        $sub1->setParent($dropdown);
        $sub1->setUrl('/sub1');
        $manager->persist($sub1);

        $sub2 = new MenuEntry();
        $sub2->setTitle('sub2');
        $sub2->setParent($dropdown);
        $sub2->setUrl('/sub2');
        $manager->persist($sub2);

        $subsub1 = new MenuEntry();
        $subsub1->setTitle('subsub1');
        $subsub1->setParent($sub2);
        $subsub1->setUrl('/subsub1');
        $manager->persist($subsub1);

        $subsub2 = new MenuEntry();
        $subsub2->setTitle('subsub2');
        $subsub2->setParent($sub2);
        $subsub2->setUrl('/subsub2');
        $manager->persist($subsub2);

        //Create a second menu
        $secondmenu=new MenuEntry();
        $secondmenu->setTitle('second');
        $manager->persist($secondmenu);

        $shome = new MenuEntry();
        $shome->setTitle('Home');
        $shome->setParent($secondmenu);
        $shome->setUrl('/');
        $manager->persist($shome);

        $topmenu = new MenuEntry();
        $topmenu->setTitle('topmenu');
        $topmenu->setParent($secondmenu);
        $topmenu->setUrl('/topmenu');
        $manager->persist($topmenu);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     * The Order the fixture should be loaded. The lower the sooner it gets loaded.
     */
    public function getOrder()
    {
        return 1;
    }
}