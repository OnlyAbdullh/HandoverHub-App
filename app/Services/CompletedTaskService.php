<?php

namespace App\Services;

use App\Repositories\Contracts\CompletedTaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Exception;

class CompletedTaskService
{
    protected $completedTaskRepository;

    public function __construct(CompletedTaskRepositoryInterface $completedTaskRepository)
    {
        $this->completedTaskRepository = $completedTaskRepository;
    }

    public function getAllTasks(): Collection
    {
        return $this->completedTaskRepository->all();
    }

    public function createTask(array $data): array
    {
        try {
            $task = $this->completedTaskRepository->create($data);

            return [
                'success' => true,
                'message' => 'Completed Task created successfully',
                'data'    => $task,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create Completed Task: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    public function updateTask(int $id, array $data): array
    {
        try {
            $task = $this->completedTaskRepository->find($id);

            if (! $task) {
                return [
                    'success' => false,
                    'message' => 'Completed Task not found',
                    'data'    => null,
                ];
            }

            $updated = $this->completedTaskRepository->update($id, $data);

            if ($updated) {
                $updatedTask = $this->completedTaskRepository->find($id);
                return [
                    'success' => true,
                    'message' => 'Completed Task updated successfully',
                    'data'    => $updatedTask,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to update Completed Task',
                'data'    => null,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update Completed Task: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    public function deleteTask(int $id): array
    {
        try {
            $task = $this->completedTaskRepository->find($id);

            if (! $task) {
                return [
                    'success' => false,
                    'message' => 'Completed Task not found',
                    'data'    => null,
                ];
            }

            $deleted = $this->completedTaskRepository->delete($id);

            if ($deleted) {
                return [
                    'success' => true,
                    'message' => 'Completed Task deleted successfully',
                    'data'    => null,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to delete Completed Task',
                'data'    => null,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete Completed Task: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }
}
