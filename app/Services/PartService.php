<?php

namespace App\Services;

use App\Repositories\Contracts\PartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Part;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartService
{
    protected $partRepository;

    public function __construct(PartRepositoryInterface $partRepository)
    {
        $this->partRepository = $partRepository;
    }

    public function getAllParts(): Collection
    {
        try {
            return $this->partRepository->all();
        } catch (Exception $e) {
            Log::error('Error fetching parts: ' . $e->getMessage());
            throw new Exception('فشل في جلب القطع');
        }
    }

    public function getPartById($id): ?Part
    {
        try {
            $part = $this->partRepository->find($id);
            if (!$part) {
                throw new Exception('القطعة غير موجودة');
            }
            return $part;
        } catch (Exception $e) {
            Log::error('Error fetching part: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createPart(array $data): Part
    {
        try {
            DB::beginTransaction();

            $partData = [
                'name' => $data['name'],
                'code' => $data['code'],
                'is_general' => $data['is_general'],
            ];

            $part = $this->partRepository->create($partData);

            if (isset($data['engine_ids']) && !empty($data['engine_ids'])) {
                $this->partRepository->attachEngines($part->id, $data['engine_ids']);
            }

            DB::commit();
            return $this->partRepository->find($part->id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating part: ' . $e->getMessage());
            throw new Exception('فشل في إنشاء المادة');
        }
    }

    public function updatePart($id, array $data): Part
    {
        try {
            DB::beginTransaction();

            $partData = [];
            if (isset($data['name'])) {
                $partData['name'] = $data['name'];
            }
            if (isset($data['code'])) {
                $partData['code'] = $data['code'];
            }
            if (isset($data['is_general'])) {
                $partData['is_general'] = $data['is_general'];
            }

            $updated = $this->partRepository->update($id, $partData);
            if (!$updated) {
                throw new Exception('part is not exist');
            }

            /*   if (isset($data['engine_ids'])) {
                   $this->partRepository->syncEngines($id, $data['engine_ids']);
               }*/

            DB::commit();
            return $this->partRepository->find($id);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating part: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteParts(array $ids): bool
    {
        try {
            DB::beginTransaction();

            $deleted = $this->partRepository->deleteMany($ids);

            if ($deleted === 0) {
                throw new Exception('Parts not found');
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting parts: ' . $e->getMessage());
            throw $e;
        }
    }

}
