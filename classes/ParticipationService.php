<?php

class ParticipationService extends AbstractService
{
    public function __construct(private ParticipantRepositoryInterface $participants)
    {
    }

    public function aanmelden(int $userId, int $activityId): bool
    {
        if ($userId <= 0 || $activityId <= 0) {
            return false;
        }

        return $this->participants->aanmelden($userId, $activityId);
    }

    public function afmelden(int $userId, int $activityId): bool
    {
        if ($userId <= 0 || $activityId <= 0) {
            return false;
        }

        return $this->participants->afmelden($userId, $activityId);
    }

    public function isAangemeld(int $userId, int $activityId): bool
    {
        if ($userId <= 0 || $activityId <= 0) {
            return false;
        }

        return $this->participants->isAangemeld($userId, $activityId);
    }

    public function getDeelnemers(int $activityId): array
    {
        if ($activityId <= 0) {
            return [];
        }

        return $this->participants->getDeelnemers($activityId);
    }

    public function filterActivitiesForUser(int $userId, array $activities): array
    {
        if ($userId <= 0) {
            return [];
        }

        return array_values(array_filter(
            $activities,
            fn(Activity $activity): bool => $activity->getId() !== null
                && $this->participants->isAangemeld($userId, $activity->getId())
        ));
    }
}
