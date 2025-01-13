<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;



class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    //サムネイル作成関数(圧縮画像を作る)
    protected function makeThumbnail($photoPath, $thumbnailPath)
    {
        File::ensureDirectoryExists(dirname($thumbnailPath));
        Image::read($photoPath)->scaleDown(height: 500)
            ->save($thumbnailPath);
    }

    //tag投稿を登録する関数
    protected function storeTagsId($input)
    {
        //tag投稿（文字列）が空白なら処理終了
        if (!$input) {
            return false;
        }
        $tags = [];
        //タグ投稿（文字列）をスペースで区切って配列$tag_listに代入
        $tag_list = preg_split('/\s/u', $input);

        //空文字の要素を削除
        $tag_list = array_filter($tag_list);


        foreach ($tag_list as $tag) {
            //tagテーブルにnameがあればインスタンス取得、登録されていなければインスタンス生成・DB登録
            $record = Tag::firstOrCreate(['name' => $tag]);

            // 取得もしくは新規作成したtagを配列＄tagsに一つずつ追加
            array_push(
                $tags,
                $record
            );
        }
        // タグのidだけをとりだした配列$tag_idsを生成する
        $tag_ids = [];
        foreach ($tags as $tag) {
            array_push($tag_ids, $tag->id);
        }
        return $tag_ids;
    }

    //写真データをDBとstrageから消去する関数
    public function deletePhotoData($photo)
    {
        //fileサーバ内のフォルダを移動（削除の代わり)
        if (Storage::exists($photo->filename) === true) {
            Storage::move($photo->filename, 'soft-delete/' . $photo->filename);
        }
        if (Storage::exists('thumbnail/' . $photo->filename) === true) {
            Storage::move('thumbnail/' . $photo->filename, 'thumbnail/soft-delete/' . $photo->filename);
        }
        // データベースエラー時にファイルpath復活を行うため
        // トランザクションを利用する
        DB::beginTransaction();
        try {
            $photo->delete();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            // DBとファイルサーバの不整合を避けるためアップロードしたファイルを削除
            if (Storage::exists($photo->filename) !== true) {
                Storage::move('soft-delete/' . $photo->filename, $photo->filename);
            }
            if (Storage::exists('thumbnail/' . $photo->filename) !== true) {
                Storage::move('thumbnail/soft-delete/' . $photo->filename, 'thumbnail/' . $photo->filename);
            }
            throw $exception;
        }


        //tryーcatchが完了したら写真をdelete
        Storage::delete('soft-delete/' . $photo->filename);
        Storage::delete('thumbnail/soft-delete/' . $photo->filename);
    }
}
