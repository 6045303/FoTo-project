<?php

interface ParticipantRepositoryInterface
{
    public function isAangemeld(int $userId, int $activityId): bool;

    public function aanmelden(int $userId, int $activityId): bool;

    public function afmelden(int $userId, int $activityId): bool;

    public function getDeelnemers(int $activityId): array;
}
