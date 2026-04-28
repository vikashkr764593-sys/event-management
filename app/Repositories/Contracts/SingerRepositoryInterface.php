<?php

namespace App\Repositories\Contracts;

use App\Models\Singer;
use Illuminate\Database\Eloquent\Collection;

interface SingerRepositoryInterface
{
    /**
     * Retrieve all Singer records.
     *
     * @return Collection|Singer[]
     */
    public function all(): Collection;

    /**
     * Find a Singer by ID.
     *
     * @param int $id
     * @return Singer
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): Singer;

    /**
     * Create a new Singer.
     *
     * @param array $data
     * @return Singer
     */
    public function create(array $data): Singer;

    /**
     * Update an existing Singer.
     *
     * @param int $id
     * @param array $data
     * @return Singer
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Singer;

    /**
     * Delete a Singer by ID.
     *
     * @param int $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;
}