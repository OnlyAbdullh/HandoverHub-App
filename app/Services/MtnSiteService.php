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
     * Delete an MTN site
     *
     * @param int $id
     * @return bool
     * @throws MtnSiteNotFoundException
     */
    public function deleteSite($id)
    {
        try {
            DB::beginTransaction();

            $site = $this->mtnSiteRepository->findById($id);

            if (!$site) {
                throw new MtnSiteNotFoundException("MTN Site with ID {$id} not found");
            }

            // Check if site has related generators before deletion
            if ($site->generators()->exists()) {
                throw new \Exception("Cannot delete MTN Site with ID {$id} because it has related generators");
            }

            $result = $this->mtnSiteRepository->delete($site);

            DB::commit();

            return $result;
        } catch (MtnSiteNotFoundException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting MTN Site: " . $e->getMessage(), [
                'id' => $id,
                'exception' => $e
            ]);

            throw $e;
        }
    }
}
