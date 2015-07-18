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

3. Add DatabaseContext in Behat:


    default:
        suites:
            default:
                contexts:
                    - Unknown\QABundle\Behat\DatabaseContext
                        container:   '@service_container'


Result
======================

Database will be purged if and only if changes were made during previous scenario.
Database will also be purged if scenario is tagged with either @javascript or @db-purge.

Good luck!
