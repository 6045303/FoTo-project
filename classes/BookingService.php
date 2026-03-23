<?php

class BookingService extends AbstractService
{
    public function __construct(private ActivityRepositoryInterface $activities)
    {
    }

    public function saveFromPost(array $postData): bool
    {
        $activity = Activity::fromArray($postData);

        if (!$activity->canBeBookedFromTomorrow()) {
            return false;
        }

        $id = isset($postData['id']) && $postData['id'] !== '' ? (int) $postData['id'] : null;

        if ($id !== null) {
            return $this->activities->update($id, $activity);
        }

        return $this->activities->create($activity);
    }

    public function deleteById(int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        return $this->activities->delete($id);
    }

    public function getBinnenActivities(): array
    {
        return $this->activities->getBinnen();
    }

    public function getBuitenActivities(): array
    {
        return $this->activities->getBuiten();
    }

    public function getActivityById(int $id): ?Activity
    {
        return $this->activities->getById($id);
    }

    public function getAllActivities(): array
    {
        return array_merge($this->getBinnenActivities(), $this->getBuitenActivities());
    }
}
