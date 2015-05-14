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
     * @var callback
     */
    protected $initialActions;

    /**
     * Constructor.
     *
     * @param $container Container
     */
    public function __construct(Container $container, $initialActions = null)
    {
        $this->container = $container;
        $this->initialActions = $initialActions;
    }

    /**
     * Execute purger.
     *
     * @param boolean $force
     * @return null
     */
    public function execute($force = false)
    {
        if (!self::$dbIsClean || $force) {
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
        if ($this->initialActions) {
            $actions = $this->initialActions;
            $actions($em);
        }
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
