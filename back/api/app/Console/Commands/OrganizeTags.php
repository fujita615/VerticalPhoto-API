<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OrganizeTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:organize-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unused tags from the database';

    /**
     * 利用されていないタグをデータベースから削除するコマンド
     */
    public function handle()
    {
        //photoとtagの中間テーブルから利用されているtag_idのリスト（配列）を抽出
        $ids = DB::table('photo_tag')->pluck('tag_id');
        //tag_id配列から重複idを削除
        $tag_ids = $ids->unique();
        //tagsテーブルからtag_id配列に含まれないidのレコードを抽出して削除
        DB::table('tags')->whereNotIn('id', $tag_ids)->delete();
    }
}
