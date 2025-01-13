<?php

namespace App\Jobs;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class UserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    //ユーザー登録削除時にユーザーのupした写真idを受け取って
    //strageから写真とサムネイルを全て消去するjob
    public $photos;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $user = User::with('photos')->find(Auth::user()->id);
        $this->photos = $user->photos;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->photos as $photo) {
            //写真データをDBとstrageから消去する関数を使う
            $deletePhoto = new Controller();
            $deletePhoto->deletePhotoData($photo);
        }
    }
}
