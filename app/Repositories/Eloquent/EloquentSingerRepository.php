<?php

namespace App\Repositories\Eloquent;

use App\Models\Singer;
use App\Repositories\Contracts\SingerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentSingerRepository implements SingerRepositoryInterface
{

    public function all(): Collection { return Singer::with('user')->get(); }
    public function find(int $id): Singer { return Singer::with('user')->findOrFail($id); }

    public function create(array $data): Singer
    {
        return Singer::create($data);
    }

    public function update(int $id, array $data): Singer
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