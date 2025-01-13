<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTagsRequest;
use App\Models\Photo;
use App\Models\Tag;

class TagController extends Controller
{
    public function __construct()
    {
        //ユーザー認証が必要(一覧と詳細表示には不要なのでexcept)
        $this->middleware('auth');
    }
    //中間テーブルのphoto_tagテーブルに追加するメソッド
    public function addPhotoTag(Tag $tag, string $id, UpdateTagsRequest $request)
    {
        $photo = Photo::where('id', $id)->with('tags')->first();
        if ($request->name != null) {

            $tag_ids = Controller::storeTagsId($request->name);

            // 複数tag全てをphotoを通して中間テーブルphto_tagに保存
            $photo->tags()->syncWithoutDetaching($tag_ids);
            $updatePhoto = Photo::where('id', $id)->with('tags')->first();
        }
        //フォームからの投稿が完了したら登録内容を詰めた$photoとステータスコード201(成功)をフロントエンドに返す
        return response($updatePhoto, 201);
    }
    //中間テーブルのphoto_tagテーブルを削除するメソッド
    public function deletePhotoTag(Tag $tag, string $id, string $tagName)
    {
        $photo = Photo::find($id);
        $tag =  Tag::where('name', $tagName)->first();
        if (!$photo || !$tag) {
            abort(404);
        }

        $photo->tags()->detach($tag->id);
        return ["photo_id" => $id];
    }
}
