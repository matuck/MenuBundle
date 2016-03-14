<?php

namespace matuck\MenuBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Container;


class AdminControllerTest extends WebTestCase
{
    protected $_application;

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->_application->getKernel()->getContainer();
    }

    /**
     * Loads Test data for tests
     * @throws \Exception
     */
    public function setUp()
    {
        $kernel = new \AppKernel("test", true);
        $kernel->boot();
        $this->_application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $this->_application->setAutoExit(false);
        $dropdb = new ArrayInput(array("command" => "doctrine:schema:drop", "--force" => true));
        $createdb = new ArrayInput(array("command" => "doctrine:schema:create"));
        $loaddata = new ArrayInput(array("command" => "doctrine:fixtures:load", "-n" => true,
          /*
           Use this if you only want to load certain fixtures.
           "--fixtures" => __DIR__ . "/../DataFixtures"
           */
        ));
        $output = new NullOutput();
        $this->_application->run($dropdb, $output);
        $this->_application->run($createdb, $output);
        $this->_application->run($loaddata, $output);
    }

    /**
     * Test Menu() function
     * @throws \Exception
     */
    public function testMenu()
    {
        $client = static::createClient();

        /** @var Router  $router */
        $router = $this->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('matuckmenu'));

        $this->assertContains('Manage Menus', $client->getResponse()->getContent());
        $this->assertContains('main', $client->getResponse()->getContent());
        $this->assertContains('second', $client->getResponse()->getContent());
        $this->assertContains('Add New Root Menu', $client->getResponse()->getContent());
    }

    /**
     *
     * @throws \Exception
     */
    public function testMenuCreate()
    {
        $client = static::createClient();

        /** @var Router  $router */
        $router = $this->getContainer()->get('router');

        $crawler = $client->request('GET', $router->generate('matuckmenu'));
    }
}
