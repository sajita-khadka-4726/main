<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $tag_id
 * @property int $task_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Tag $tag
 * @property-read Task $task
 *
 * @method static \Database\Factories\TaskTagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskTag query()
 *
 * @mixin \Eloquent
 */
final class TaskTag extends Pivot
{
    /** @use HasFactory<\Database\Factories\TaskTagFactory> */
    use HasFactory;

    protected $table = 'task_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'tag_id',
    ];

    /**
     * TaskTag belongs to a specific Task.
     *
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * TaskTag belongs to a specific Tag.
     *
     * @return BelongsTo<Tag, $this>
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
