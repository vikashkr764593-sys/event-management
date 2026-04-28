<?php
namespace App\Services;
use App\Repositories\Contracts\InstrumentRepositoryInterface;
class InstrumentService
{
    protected $instrumentRepo;
    public function __construct(InstrumentRepositoryInterface $instrumentRepo) { $this->instrumentRepo = $instrumentRepo; }
    public function getAll() { return $this->instrumentRepo->all(); }
    public function getById($id) { return $this->instrumentRepo->find($id); }
}
