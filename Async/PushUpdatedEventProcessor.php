<?php
namespace Dfn\Bundle\OroCronofyBundle\Async;

use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;

/**
 * Class PushUpdatedEventProcessor
 * @package Dfn\Bundle\OroCronofyBundle\Async
 */
class PushUpdatedEventProcessor extends SingleBaseProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(MessageInterface $message, SessionInterface $session)
    {
        $this->setMethod('pushUpdatedEvent');
        return parent::process($message, $session);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedTopics()
    {
        return [Topics::PUSH_UPDATED_EVENT];
    }
}