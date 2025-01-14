<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

//Tagテーブルの整理　処理後結果をメールで通知
Schedule::command('app:organize-tags')->dailyAt('2:00')->emailOutputTo(env('MAIL_FROM_ADDRESS'));
//Userデータの削除　処理後結果をメールで通知
Schedule::command('app:organize-users')->dailyAt('23:50')->emailOutputTo(env('MAIL_FROM_ADDRESS'));

// queueの実行（mail送信 5分ごと）失敗したらメールで通知
Schedule::command('queue:work --stop-when-empty --queue=default')->everyFiveMinutes()->emailOutputOnFailure(env('MAIL_FROM_ADDRESS'));

// queueの実行（写真削除 1分に1件）失敗したらメールで通知
Schedule::command('queue:work --stop-when-empty --queue=delete --once')->everyMinute()->emailOutputOnFailure(env('MAIL_FROM_ADDRESS'));


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
