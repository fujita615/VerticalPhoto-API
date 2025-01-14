<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Photo;
use App\Models\Tag;
use App\Traits\Content;

class TagController extends Controller
{
    use Content;
    public function addPhotoTag(string $id, UpdateTagRequest $request)
    {
        $photo = Photo::where('id', $id)->with('tags')->first();
        if ($request->name != null) {

            $tag_ids = $this->storeTagsId($request->name);

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
