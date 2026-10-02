<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RecycleRecord;
use App\Enums\RecoverableType;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecycleBinRequest;
use App\Http\Resources\DeletedRecordResource;
use App\Support\ApiQuery;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RecycleBinController extends Controller
{
    public function index(RecycleBinRequest $request): AnonymousResourceCollection
    {
        $type = RecoverableType::from($request->validated('type'));
        $class = $type->modelClass();
        $this->authorize('viewAny', $class);
        $query = $class::onlyTrashed()->with($type->relations());
        if ($search = $request->validated('search')) {
            $query->where(function ($query) use ($search, $type): void {
                foreach ($type->searchColumns() as $column) {
                    $query->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        return DeletedRecordResource::collection($query->orderByDesc('deleted_at')->orderByDesc('id')->paginate(ApiQuery::perPage($request))->withQueryString());
    }

    public function destroy(Request $request, RecoverableType $type, int $record, RecycleRecord $action): Response
    {
        $model = $type->modelClass()::query()->findOrFail($record);
        $this->authorize('delete', $model);
        $action->delete($model, $request->user());

        return response()->noContent();
    }
}
