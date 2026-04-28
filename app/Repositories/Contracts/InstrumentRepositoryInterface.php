<?php

namespace App\Repositories\Contracts;

use App\Models\Instrument;
use Illuminate\Database\Eloquent\Collection;

interface InstrumentRepositoryInterface
{
    /**
     * Retrieve all Instrument records.
     *
     * @return Collection|Instrument[]
     */
    public function all(): Collection;

    /**
     * Find a Instrument by ID.
     *
     * @param int $id
     * @return Instrument
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): Instrument;

    /**
     * Create a new Instrument.
     *
     * @param array $data
     * @return Instrument
     */
    public function create(array $data): Instrument;

    /**
     * Update an existing Instrument.
     *
     * @param int $id
     * @param array $data
     * @return Instrument
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Instrument;

    /**
     * Delete a Instrument by ID.
     *
     * @param int $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;
}