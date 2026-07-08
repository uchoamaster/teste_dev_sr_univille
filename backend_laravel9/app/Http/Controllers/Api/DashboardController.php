<?php

namespace App\Http\Controllers\Api;

use App\Domain\Transactions\Contracts\TransactionQueryRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardSummaryResource;

class DashboardController extends Controller
{
    public function __invoke(TransactionQueryRepository $repository): DashboardSummaryResource
    {
        return new DashboardSummaryResource($repository->summary());
    }
}
