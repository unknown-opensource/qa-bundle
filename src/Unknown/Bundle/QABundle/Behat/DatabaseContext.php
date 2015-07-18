<?php
namespace Unknown\Bundle\QABundle\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Unknown\Bundle\QABundle\ORM\Cleaner;

class DatabaseContext implements Context, SnippetAcceptingContext
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Every scenario gets its own context instance.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->getCleaner()->execute();
    }

    /** @AfterScenario */
    public function after(AfterScenarioScope $scope)
    {
        if (in_array('db-purge', $scope->getScenario()->getTags())) {
            Cleaner::$dbIsClean = false;
        }
        if (in_array('javascript', $scope->getScenario()->getTags())) {
            Cleaner::$dbIsClean = false;
        }
    }

    /**
     * @return \Unknown\Bundle\QABundle\ORM\Cleaner
     */
    protected function getCleaner()
    {
        $initialActions = function(EntityManager $em) {

        };
        return new Cleaner($this->container, $initialActions);
    }
}
