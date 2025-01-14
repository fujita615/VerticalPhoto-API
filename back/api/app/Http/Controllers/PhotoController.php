<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhotoRequest;
use App\Jobs\DeletePhotoJob;
use App\Models\Photo;
use App\Traits\Content;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    use Content; //共通処理トレイトを呼び出す

    /*写真の一覧表示*/
    public function index()
    {
        $photos = Photo::with(['owner:id,nickname', 'likes', 'tags'])->orderBy(Photo::CREATED_AT, 'desc')->get();
        //getの代わりにpagenateを使うことでページネーション用に総ページと現在ページ数を示す情報が追加される
        // ->paginate();
        return $photos;
    }

    /*写真のアップロードとサムネイル作成・タグ付け一括処理*/
    public function create(StorePhotoRequest $request)
    {
        //投稿写真データの拡張子を取得
        $extension = $request->photo->extension();

        //Modelからインスタンス（1レコード）を作成
        $photo = new Photo();

        //idをulidで生成
        $photo->id = Str::ulid();
        //idと拡張子をくっつけてfilenameとする
        $photo->filename = $photo->id . '.' . $extension;

        //一時保存
        Storage::disk('temp')->putFileAs('', $request->photo, $photo->filename, 'public');
        //ContentトレイトにまとめたmakeThumbnail関数でサムネイル作成
        $this->makeThumbnail(
            Storage::disk('temp')->path($photo->filename),
            Storage::disk('temp')->path($photo->filename)
        );

        //一時保存ディレクトリ（local)からあらためてファイルを取り直す
        //strageクラスで扱うためのインスタンスはuploadedFileクラスで作成する
        $thumbnail = new UploadedFile(storage_path("app/temp/" . $photo->filename), $photo->filename);

        //保存
        //DL用のオリジナル
        Storage::putFileAs('', $request->photo, $photo->filename, 'public');
        //表示用のサムネイル版
        Storage::putFileAs('thumbnail',  $thumbnail,  $photo->filename, 'public');
        //一時ファイルを消す
        Storage::disk('temp')->delete($photo->filename);

        // データベースエラー時にファイル削除を行うため
        // トランザクションを利用する
        DB::beginTransaction();
        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            // DBとファイルサーバの不整合を避けるためアップロードしたファイルを削除
            if (Storage::exists($photo->filename) === true) {
                Storage::delete($photo->filename);
            }
            if (Storage::exists('thumbnail/' . $photo->filename) === true) {
                Storage::delete('thumbnail/' . $photo->filename);
            }
            throw $exception;
        }

        //投稿フォームからタグの投稿もあった場合
        if ($request->tags != null) {
            $tag_ids = $this->storeTagsId($request->tags);

            // 複数tag全てをphotoを通して中間テーブルphto_tagに保存
            $photo->tags()->sync($tag_ids);
        }
        //フォームからの投稿が完了したら登録内容を詰めた$photoとステータスコード201(成功)をフロントエンドに返す
        return response($photo, 201);
    }
    /* 写真のダウンロード */
    public function download(Photo $photo)
    {
        //レスポンスヘッダ Content-Disposition に attachment と filename を指定する

        if (!Storage::exists($photo->filename)) {
            abort(404);
        }
        $disposition = 'attachment; filename="' . $photo->filename . '"';
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => $disposition,
        ];
        // 画像サーバから取得した画像ファイル名とstates(200),
        // ページとして表示するのではなく、
        // ダウンロードさせるために保存ダイアログを開く指示を含めたヘッダを
        //ブラウザにレスポンスする
        return response(Storage::get($photo->filename), 200, $headers);
    }

    /*写真一枚の詳細表示 */
    public function show(string $id)
    { //photoモデルに定義したcommentsリレーションを使って繋がったcommentモデルのauthorメソッドを使って投稿者(userTable)の情報を取得
        $photo = Photo::where('id', $id)->with(['owner:id,nickname',  'comments.author:id,nickname', 'likes', 'tags'])->first();
        return $photo ?? abort(404);
    }
    /*写真の削除 */
    public function destroy(Photo $photo)
    {
        //写真データをDBとstrageから消去する非同期処理をjobに追加
        // DeletePhotoJob::dispatch($photo)->onQueue('delete');

        //写真データをDBとstrageから消去するトレイトを呼び出す
        $this->deletePhotoData($photo);

        // ステータスコード200(成功)をフロントエンドに返す
        return response(200);
    }
}
