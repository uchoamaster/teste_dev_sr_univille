<?php

namespace App\Http\Controllers\Api;

use App\Application\Transactions\Actions\ImportTransactionsAction;
use App\Domain\Transactions\Contracts\TransactionQueryRepository;
use App\Domain\Transactions\Exceptions\ExternalTransactionsUnavailable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportTransactionsRequest;
use App\Http\Requests\ListTransactionsRequest;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function index(ListTransactionsRequest $request, TransactionQueryRepository $repository)
    {
        return TransactionResource::collection(
            $repository->paginate($request->filters(), $request->perPage()),
        );
    }

    public function import(ImportTransactionsRequest $request, ImportTransactionsAction $action): JsonResponse
    {
        try {
            $batch = $action->execute($request->source(), $request->limit());
        } catch (ExternalTransactionsUnavailable $exception) {
            return response()->json([
                'message' => 'The external transaction source is currently unavailable.',
                'details' => $exception->getMessage(),
            ], 503);
        }

        return response()->json([
            'message' => 'Transactions queued for processing.',
            'batch' => [
                'id' => $batch->id,
                'source' => $batch->source,
                'status' => $batch->status,
                'queued_count' => $batch->queued_count,
                'started_at' => $batch->started_at?->toIso8601String(),
            ],
        ], 202);
    }
}
