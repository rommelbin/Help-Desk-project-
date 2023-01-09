<?php

namespace App\Listeners;

use App\Events\CreateNotificationsCommentEvent;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon as Carbon;

class CreateNotificationsCommentListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param CreateNotificationsCommentEvent $event
     * @return void
     */
    public function handle(CreateNotificationsCommentEvent $event)
    {
        $translated_arr = $this->getTranslatedAttributes();

        $attr = key($event->attributes);

        $last_attr = $event->model->$attr;
        $new_attr = $event->attributes[$attr];

        switch ($attr) {
            case 'description':
                $description = "Изменил(а) $translated_arr[$attr]";
                break;
            case 'completed_at':
            case 'deadline':
                $last_attr = Carbon::createFromDate($last_attr)->addHours(7)->format('d.m.Y H:i');
                $new_attr = Carbon::createFromDate($new_attr)->addHours(7)->format('d.m.Y H:i');
                $description = "Изменил(а) $translated_arr[$attr] с '$last_attr' на '$new_attr'";
                break;
            case 'executor_id':
                $new_executor = User::find($new_attr)->name;
                if (is_null($last_attr)) {
                    $description = "Изменил(а) $translated_arr[$attr] на '$new_executor'";
                } else {
                    $last_executor = User::find($last_attr)->name;
                    $description = "Изменил(а) $translated_arr[$attr] с '$last_executor' на '$new_executor'";
                }
                break;
            case 'private':
                switch ($last_attr) {
                    case true:
                        $last_attr = 'Приватная';
                        break;
                    default:
                        $last_attr = 'Открытая';
                        break;
                }
                $new_attr = ($new_attr === true) ? 'Приватная' : 'Открытая';
                $description = "Изменил(а) $translated_arr[$attr] с '$last_attr' на '$new_attr'";
                break;
            default:
                $description = "Изменил(а) $translated_arr[$attr] с '$last_attr' на '$new_attr'";
        }
        $this->createComment($event->model->id, $description);
    }

    public function getTranslatedAttributes()
    {
        return [
            'description' => 'описание',
            'name' => 'название',
            'priority' => 'приоритет',
            'deadline' => 'дедлайн',
            'executor_id' => 'исполнитель',
            'status' => 'статус',
            'completed_at' => 'дата исполнения',
            'private' => 'приватность'
        ];
    }
    public function createComment(int $task_id, string $description)
    {
        $attributes_comment = [
            'task_id' => $task_id,
            'user_id' => auth()->id(),
            'description' => $description
        ];
        Comment::storeModel(null, $attributes_comment);
    }
}
