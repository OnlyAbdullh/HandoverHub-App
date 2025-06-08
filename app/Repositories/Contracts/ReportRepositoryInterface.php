<?php

namespace App\Repositories\Contracts;

use App\Models\Report;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReportRepositoryInterface
{
    /**
     * إضافة تقرير جديد مع بياناته الأساسية.
     * @param array $data
     * @return Report
     */
    public function create(array $data): Report;

    /**
     * تحديث بيانات التقرير الأساسية.
     * @param int $reportId
     * @param array $data
     * @return Report|null
     */
    public function updateBasic(int $reportId, array $data): ?Report;

    /**
     * استرجاع تفاصيل تقرير (بجميع العلاقات).
     * @param int $reportId
     * @return Report|null
     */
    public function findByIdWithRelations(int $reportId): ?Report;

    /**
     * استرجاع جميع التقارير بدون فلترات (صفحة افتراضية).
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator;

    /**
     * حذف التقرير (بما في ذلك العلاقات المرتبطة).
     * @param int $reportId
     * @return bool
     */
    public function delete(int $reportId): bool;
}
