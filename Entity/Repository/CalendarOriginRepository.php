<?php
namespace Dfn\Bundle\OroCronofyBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Dfn\Bundle\OroCronofyBundle\Entity\CalendarOrigin;
use Dfn\Bundle\OroCronofyBundle\Manager\CronofySyncHandler;

/**
 * CalendarOrigin repository
 */
class CalendarOriginRepository extends EntityRepository
{
    /**
     * Get calendar events for the users calendar tied to the passed CalendarOrigin that are not tracked.
     *
     * @param CalendarOrigin $calendarOrigin
     *
     * @return array
     */
    public function getEventsToSync(CalendarOrigin $calendarOrigin)
    {
        $originId = $calendarOrigin->getId();
        $userId = $calendarOrigin->getOwner()->getId();
        $orgId = $calendarOrigin->getOrganization()->getId();
        $repository = $this->getEntityManager();

        $from = new \DateTime("now -".CronofySyncHandler::DAYS_BACK."days");
        $to = new \DateTime("now +".CronofySyncHandler::DAYS_FORWARD."days");

        $query = $repository->createQueryBuilder()
                            ->select(['e.id'])
                            ->from('OroCalendarBundle:CalendarEvent', 'e')
                            ->join('e.calendar', 'c')
                            ->leftJoin(
                                'DfnOroCronofyBundle:CronofyEvent',
                                'ce',
                                'WITH',
                                'ce.calendarEvent = e.id AND ce.calendarOrigin = :originId'
                            )
                            ->andWhere(
                                'ce.id IS NULL',
                                'e.start > :from',
                                'e.end < :to',
                                'e.cancelled = FALSE',
                                'c.owner = :userId',
                                'c.organization = :orgId'
                            )
                            ->setParameters(
                                [
                                    'orgId' => $orgId,
                                    'userId' => $userId,
                                    'originId' => $originId,
                                    'from' => $from,
                                    'to' => $to
                                ]
                            )
                            ->getQuery();

        $results = $query->getResult();
        $results = array_merge($results, $this->getRecurringEventsToSync($calendarOrigin));

        return $results;
    }

    /**
     * Get recurring calendar events for the users calendar tied to the passed CalendarOrigin
     * that have not past the end date and are after the last push date.
     *
     * @param CalendarOrigin $calendarOrigin
     *
     * @return array
     */
    public function getRecurringEventsToSync(CalendarOrigin $calendarOrigin)
    {
        $userId = $calendarOrigin->getOwner()->getId();
        $orgId = $calendarOrigin->getOrganization()->getId();
        $repository = $this->getEntityManager();

        $from = $calendarOrigin->getLastPushedAt();
        $to = new \DateTime("now +".CronofySyncHandler::DAYS_FORWARD."days");

        $query = $repository->createQueryBuilder()
                            ->select(['e.id'])
                            ->from('OroCalendarBundle:CalendarEvent', 'e')
                            ->join('e.calendar', 'c')
                            ->leftJoin(
                                'e.recurrence',
                                'r'
                            )
                            ->andWhere(
                                'r.calculatedEndTime > :from',
                                'r.calculatedEndTime < :to',
                                'e.cancelled = FALSE',
                                'c.owner = :userId',
                                'c.organization = :orgId'
                            )
                            ->setParameters(
                                [
                                    'orgId' => $orgId,
                                    'userId' => $userId,
                                    'from' => $from,
                                    'to' => $to
                                ]
                            )
                            ->getQuery();

        return $query->getResult();
    }
}
