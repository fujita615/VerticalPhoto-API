<?php

namespace App\Console\Commands;

use App\Jobs\DeletePhotoJob;

use App\Models\User;
use App\Traits\Content;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class OrganizeUsers extends Command
{
    use Content;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:organize-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //userデータからrole指定の無い昨日作成したデータを削除する

        $users = User::with('photos')->where('role', null)->whereDate('created_at', Carbon::yesterday())->get();
        foreach ($users as $user) {
            $photos = $user->photos;
            if ($photos) {
                foreach ($photos as $photo) {
                    // 投稿写真の消去は非同期処理
                    DeletePhotoJob::dispatch($photo)->onQueue('delete');
                }
            }
            $user->delete();
        }
    }
}
