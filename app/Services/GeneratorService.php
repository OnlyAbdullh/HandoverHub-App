<?php

namespace App\Services;

use App\Http\Resources\GeneratorResource;
use App\Repositories\Contracts\GeneratorRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneratorService
{
    protected GeneratorRepositoryInterface $generatorRepository;

    public function __construct(GeneratorRepositoryInterface $generatorRepository)
    {
        $this->generatorRepository = $generatorRepository;
    }

    /**
     * Get all generators
     *
     * @return array
     */
    /**
     * ترجع كل المولدات أو المولدات غير المربوطة حسب قيمة $onlyUnassigned
     *
     * @param  bool  $onlyUnassigned
     * @return array
     */
    public function getAllGenerators(bool $onlyUnassigned = false): array
    {
        try {
            $generators = $this->generatorRepository->getAllWithRelations($onlyUnassigned);

            return [
                'data'    => GeneratorResource::collection($generators),
                'message' => $onlyUnassigned
                    ? 'Unassigned generators retrieved successfully'
                    : 'All generators retrieved successfully',
                'status'  => 200,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getAllGenerators: ' . $e->getMessage());

            return [
                'data'    => [],
                'message' => 'Error retrieving generators',
                'status'  => 500,
            ];
        }
    }

    /**
     * Get generator details by ID
     *
     * @param int $id
     * @return array
     */
    public function getGeneratorDetails(int $id): array
    {
        try {
            $generator = $this->generatorRepository->findWithRelations($id);

            if (!$generator) {
                return [
                    'data' => null,
                    'message' => 'Generator not found',
                    'status' => 404
                ];
            }

            return [
                'data' => new GeneratorResource($generator),
                'message' => 'Generator details retrieved successfully',
                'status' => 200
            ];
        } catch (\Exception $e) {
            Log::error('Error retrieving generator details: ' . $e->getMessage());

            return [
                'data' => null,
                'message' => 'Error retrieving generator details',
                'status' => 500
            ];
        }
    }

    /**
     * Create new generator
     *
     * @param array $data
     * @return array
     */
    public function createGenerator(array $data): array
    {
        try {
            DB::beginTransaction();

            $generator = $this->generatorRepository->create($data);
            $generatorWithRelations = $this->generatorRepository->findWithRelations($generator->id);
            //   \Log::info('Generator with relations:', (array)$generatorWithRelations->toArray());
            DB::commit();

            return [
                'data' => new GeneratorResource($generatorWithRelations),
                'message' => 'Generator created successfully',
                'status' => 201
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating generator: ' . $e->getMessage());

            return [
                'data' => null,
                'message' => 'Error creating generator',
                'status' => 500
            ];
        }
    }

    /**
     * Update generator
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateGenerator(int $id, array $data): array
    {
        try {
            if (!$this->generatorRepository->exists($id)) {
                return [
                    'data' => null,
                    'message' => 'Generator not found',
                    'status' => 404
                ];
            }

            DB::beginTransaction();

            $generator = $this->generatorRepository->update($id, $data);
            $generatorWithRelations = $this->generatorRepository->findWithRelations($generator->id);

            DB::commit();

            $responseMessage = $this->getUpdateMessage($data);

            return [
                'data' => new GeneratorResource($generatorWithRelations),
                'message' => $responseMessage,
                'status' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating generator: ' . $e->getMessage());

            return [
                'data' => null,
                'message' => 'Error updating generator',
                'status' => 500
            ];
        }
    }

    /**
     * Delete generator
     *
     * @param int $id
     * @return array
     */
    public function deleteGenerator(int $id): array
    {
        try {
            if (!$this->generatorRepository->exists($id)) {
                return [
                    'data' => null,
                    'message' => 'Generator not found',
                    'status' => 404
                ];
            }

            DB::beginTransaction();

            $this->generatorRepository->delete($id);

            DB::commit();

            return [
                'data' => null,
                'message' => 'Generator deleted successfully',
                'status' => 200
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting generator: ' . $e->getMessage());

            return [
                'data' => null,
                'message' => 'Error deleting generator',
                'status' => 500
            ];
        }
    }


    /**
     * Get appropriate update message based on data
     *
     * @param array $data
     * @return string
     */
    private function getUpdateMessage(array $data): string
    {
        if (isset($data['initial_meter']) && isset($data['site_id'])) {
            return 'Generator meter and site updated successfully';
        }

        if (isset($data['initial_meter'])) {
            return 'Generator meter updated successfully';
        }

        if (isset($data['mtn_site_id'])) {
            if (is_null($data['mtn_site_id'])) {
                return 'Generator unassigned from site successfully';
            }
            return 'Generator assigned to site successfully';
        }

        return 'Generator updated successfully';
    }
}
