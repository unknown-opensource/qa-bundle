<?php
namespace Unknown\Bundle\QABundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class DoctrineListener implements EventSubscriber
{
    /**
     * @var callback
     */
    public static $removeCallback;

    /**
     * @var callback
     */
    public static $persistCallback;

    /**
     * @var callback
     */
    public static $updateCallback;

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array('postRemove', 'postPersist', 'postUpdate');
    }

    /**
     * Execute callback after remove event.
     *
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        if (self::$removeCallback) {
            $callback = self::$removeCallback;
            $callback($args);
        }
    }

    /**
     * Execute callback after persist event.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if (self::$persistCallback) {
            $callback = self::$persistCallback;
            $callback($args);
        }
    }

    /**
     * Execute callback after update event.
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if (self::$updateCallback) {
            $callback = self::$updateCallback;
            $callback($args);
        }
    }
}
