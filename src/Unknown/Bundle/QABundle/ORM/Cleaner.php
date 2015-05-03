<?php
namespace Unknown\Bundle\QABundle\ORM;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Unknown\Bundle\QABundle\EventListener\DoctrineListener;

class Cleaner
{
    public static $dbIsClean = false;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param $container Container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Execute purger.
     *
     * @return null
     */
    public function execute()
    {
        if (!self::$dbIsClean) {
            $this->purgeAndLoad();
        }
        $this->registerCallbacks();
        self::$dbIsClean = true;
    }

    /**
     * Purge database and load fixtures.
     *
     * @return null
     */
    protected function purgeAndLoad()
    {
        $loader = new ContainerAwareLoader($this->container);
        foreach ($this->container->get('kernel')->getBundles() as $bundle) {
            /** @var $bundle Bundle */
            $path = $bundle->getPath().'/DataFixtures/ORM';
            if (!is_dir($path)) {
                continue;
            }
            $loader->loadFromDirectory($path);
        }
        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            return;
        }
        $em = $this->container->get('doctrine.orm.entity_manager'); /** @var $em \Doctrine\ORM\EntityManager */
        $purger = new ORMPurger($em);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(null);
        $executor->execute($fixtures, false);
    }

    /**
     * Register callbacks.
     */
    protected function registerCallbacks()
    {
        $callback = function() {
            Cleaner::$dbIsClean = false;
        };
        DoctrineListener::$removeCallback = $callback;
        DoctrineListener::$updateCallback = $callback;
        DoctrineListener::$persistCallback = $callback;
    }
}
