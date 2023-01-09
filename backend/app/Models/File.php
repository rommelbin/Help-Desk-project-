<?php

namespace App\Models;


use App\Exceptions\FileException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class File extends BaseModel
{
    protected
        $table = 'files',
        $primaryKey = 'id',
        $guarded = [];

    protected static array
        $storeRules = [
        'path' => ['required', 'string'],
        'extension' => ['required', 'in:jpg,jpeg,png,doc,docx,xls,xlsx,txt,pdf'],
        'comment_id' => ['required', 'integer', 'exists:comments,id'],
        'size' => ['required', 'max:5242880']
    ],
        $updateRules = [
        'path' => ['sometimes', 'required', 'string', 'regex:\d'],
        'extension' => ['required', 'in:jpg,jpeg,png,doc,docx,xls,xlsx,txt,pdf'],
        'comment_id' => ['sometimes', 'required', 'integer', 'exists:comments,id'],
        'size' => ['required']
    ],
        $events = [
        'store' => [],
        'update' => []
    ];

    /**
     * @Relation
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    public static function destroyModel(?int $id, array $attributes): array
    {
        static::askAccessToModel('deleteModel', static::class);
        $model = static::checkIDAndModel($id);

        Storage::delete($model->path);
        $model->delete();
        return ['data' => 'Deleted', 'status' => Response::HTTP_OK];
    }
}
