<?php
namespace App\Services;
use App\Repositories\Contracts\SingerRepositoryInterface;
class SingerService
{
    protected $singerRepo;
    public function __construct(SingerRepositoryInterface $singerRepo) { $this->singerRepo = $singerRepo; }
    public function getAll() { return \App\Models\Singer::with('user')->get(); }
    public function getById($id) { return \App\Models\Singer::with('user')->findOrFail($id); }
}
