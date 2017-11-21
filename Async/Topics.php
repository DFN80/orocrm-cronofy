<?php

namespace Dfn\Bundle\OroCronofyBundle\Async;

/**
 * Class Topics
 * Constants for list of messageQueue topics
 * @package Dfn\Bundle\OroCronofyBundle\Async
 */
class Topics
{
    const PUSH_NEW_EVENTS = 'oro_cronofy.push.new.events';  //Allows a array of new events to be sent
    const PUSH_NEW_EVENT = 'oro_cronofy.push.new.event';  //Send single new event
    const PUSH_UPDATED_EVENTS = 'oro_cronofy.push.updated.events'; //Allows an array of updated events to be sent
    const PUSH_UPDATED_EVENT = 'oro_cronofy.push.updated.event'; //Send single updated event
    const PUSH_DELETED_EVENTS = 'oro_cronofy.push.deleted.events'; //Allows an array of deleted events to be sent
    const PUSH_DELETED_EVENT = 'oro_cronofy.push.deleted.event'; //Send single deleted event
    const PULL_EVENT = 'oro_cronofy.pull.event';
    const PULL_EVENTS = 'oro_cronofy.pull.events';  //Pull multiple events based on date range
}