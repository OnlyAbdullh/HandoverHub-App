<?php


namespace App\Services;

use App\Exceptions\MtnSiteNotFoundException;
use App\Repositories\MtnSiteRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MtnSiteService
{
    protected $mtnSiteRepository;

    /**
     * Constructor to inject dependencies
     *
     * @param MtnSiteRepository $mtnSiteRepository
     */
    public function __construct(MtnSiteRepository $mtnSiteRepository)
    {
        $this->mtnSiteRepository = $mtnSiteRepository;
    }

    /**
     * Get all MTN sites with filtering capabilities
     *
     * @param array $filters
     */
    public function getAllSites(array $filters = [])
    {
        return $this->mtnSiteRepository->getAllWithFilters($filters);
    }

    /**
     * Create a new MTN site
     *
     * @param array $data
     * @return \App\Models\MtnSite
     */
    public function createSite(array $data)
    {
        try {
            DB::beginTransaction();

            $site = $this->mtnSiteRepository->create($data);

            DB::commit();

            return $site;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating MTN Site: " . $e->getMessage(), [
                'data' => $data,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * Update an existing MTN site
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\MtnSite
     * @throws MtnSiteNotFoundException
     */
    public function updateSite($id, array $data)
    {
        try {
            DB::beginTransaction();

            $site = $this->mtnSiteRepository->findById($id);

            if (!$site) {
                throw new MtnSiteNotFoundException("MTN Site with ID {$id} not found");
            }

            $updatedSite = $this->mtnSiteRepository->update($site, $data);

            DB::commit();

            return $updatedSite;
        } catch (MtnSiteNotFoundException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating MTN Site: " . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    /**
     * حذف عدة مواقع دفعة واحدة مع التحقق من العلاقات
     *
     * @param int[] $ids
     * @return array{deleted: int[], skipped: int[]}
     *
     * @throws \Exception عند حدوث خطأ غير متوقع
     */
    public function deleteSites(array $ids): array
    {
        DB::beginTransaction();

        try {
            $deletedCount = $this->mtnSiteRepository->deleteManyByIds($ids);

            DB::commit();

            return [
                'deleted_count' => $deletedCount
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting MTN Sites batch: ' . $e->getMessage(), [
                'ids' => $ids,
                'exception' => $e,
            ]);
            throw $e;
        }
    }


    public function getGeneratorsBySiteId(int $siteId)
    {
        return $this->mtnSiteRepository->getGenerators($siteId);
    }
}
