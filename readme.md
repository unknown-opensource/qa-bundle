Features
======================
This bundle provides you with:

- Database purge only if changes were made

Issues and feature requests are tracked in the Github issue tracker https://github.com/unknown-opensource/qa-bundle/issues

Installation
======================

1. To install this bundle with Composer, just add the following to your composer.json file:


    require: {
        ...
        "unknown/qa-bundle": "1.0.2"
    }


2. Then register the bundle in AppKernel::registerBundles()


    $bundles = array(
        ...
        new Unknown\Bundle\QABundle\UnknownQABundle(),
    );


Usage in behat
======================

Add following lines in FeatureContext:

    /** @BeforeScenario */
    public function before(\Behat\Behat\Hook\Scope\BeforeScenarioScope $scope)
    {
        (new \Unknown\Bundle\QABundle\ORM\Cleaner($this->container))->execute();
    }

Database will be purged only if changes were made during previous scenario.
