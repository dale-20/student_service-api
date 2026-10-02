<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RecycleRecord;
use App\Enums\RecoverableType;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RecordRecoveryController extends Controller
{
    public function store(RecoverableType $type, int $record, RecycleRecord $action): JsonResponse
    {
        $model = $type->modelClass()::onlyTrashed()->findOrFail($record);
        $this->authorize('restore', $model);
        $action->restore($model);

        return response()->json(['message' => 'Record recovered successfully.']);
    }
}
