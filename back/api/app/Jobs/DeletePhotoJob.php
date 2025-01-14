<?php

namespace App\Jobs;

use App\Models\Photo;
use App\Traits\Content;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;


class DeletePhotoJob implements ShouldQueue
{
    use Queueable, Content;

    protected $photo;
    /**
     * Create a new job instance.
     */
    public function __construct(Photo $photo)
    {
        //
        $this->photo = $photo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //トレイトの写真削除メソッドを非同期処理で呼び出す
        $this->deletePhotoData($this->photo);
    }
}
