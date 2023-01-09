<?php

namespace App\Listeners;

use App\Events\CreateFileEvent;
use App\Exceptions\FileException;
use App\Models\Comment;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CreateFileListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param CreateFileEvent $event
     * @return void
     * @throws \Exception
     */
    public function handle(CreateFileEvent $event)
    {
        if (isset($event->attributes['files'])) {

            $files = $event->attributes['files'];
            if (count($files) > 5)
                $this->deleteComment($event->model->id, 'Файлов больше 5');

            for ($i = 0; $i < count($files); $i++) {
                $this->createFile($files[$i], $event->model->id);
            }
        }

    }

    /**
     * @throws \Exception
     */
    private function deleteComment(string $id, string $error_text)
    {
        Comment::find($id)->delete();
        throw new \Exception($error_text, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws FileException
     * @throws \Exception
     */
    private function createFile($file, $comment_id)
    {
        $filename = Str::slug(random_bytes('15') . $file->getClientOriginalName(), '.');
        $attributes = $this->prepareAttributes($file, $comment_id,$filename);

        $this->saveFile($file, $attributes, $filename);
    }

    /**
     * @throws \Exception
     */
    private function prepareAttributes(UploadedFile $file, $comment_id, $filename)
    {
        return [
            'path' => 'storage/' . $filename,
            'extension' => $file->getClientOriginalExtension(),
            'comment_id' => $comment_id,
            'size' => $file->getSize()
        ];
    }

    private function saveFile($file, $attributes, $filename)
    {
        if (File::storeModel($id = null, $attributes)['status'] !== 201)
            $this->deleteComment($attributes['comment_id'], 'Файл не прошёл валидацию');

        Storage::disk('local')->put('./public/' . $filename, $file->getContent());
    }
}
