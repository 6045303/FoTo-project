<?php

interface ActivityRepositoryInterface
{
    public function getBinnen(): array;

    public function getBuiten(): array;

    public function getById(int $id): ?Activity;

    public function getByType(string $type): array;

    public function create(Activity $activity): bool;

    public function update(int $id, Activity $activity): bool;

    public function delete(int $id): bool;
}
