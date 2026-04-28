<?php

namespace App\Repositories\Eloquent;

use App\Models\Instrument;
use App\Repositories\Contracts\InstrumentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentInstrumentRepository implements InstrumentRepositoryInterface
{

    public function all(): Collection { return Instrument::all(); }
    public function find(int $id): Instrument { return Instrument::findOrFail($id); }

    public function create(array $data): Instrument
    {
        return Instrument::create($data);
    }

    public function update(int $id, array $data): Instrument
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        return $record->delete();
    }
}