# 縦長写真共有サービス VerticalPhoto-API ver.2
縦長写真専用のアップロード・ダウンロードができるWEBサイトです。

## ver.1からの変更点
 フロントエンドとバックエンドを分離して別サーバーに配置
  ### backend 
  - laravelをver.10から11に変更
  #### 開発環境
  - WEBserverコンテナをamazonLinux(Redhat系)からdebian系に変更 
  - キャッシュ管理用にredisコンテナを追加 


## 背景
ポートフォリオとして制作したWEBサービスのサンプルです.

## URL
https://www.yf5160.com

## 機能
- 写真の一覧表示 詳細表示 タグ検索 
- ユーザー情報登録・更新・削除
- 写真の投稿（タグ付け）・削除・ダウンロード
- 他ユーザーの投稿写真へのいいね・コメント・ダウンロード

## 使用言語
- PHP(Laravel __ver.11__)

## 開発環境
### backend
Docker 
  - php-fpm
  - apache2
  - mysql 
  - __redis__(キャッシュ管理用に追加)


