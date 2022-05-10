<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ユーザー登録はLaravel-uiを使うとコマンド一発だが今回は自作
// ↓の記述でユーザー登録(Authorize:認証)に関するLaravelのデフォルトのアクション、ルーティングが登録される
Auth::routes();

// 個別のルーティング、コントローラーの記述
// ルート名を追加したデフォルトのArticleに合わせる
Route::get('/', 'ArticleController@index')->name('articles.index');

// 一覧,個別表示,新規登録,更新,削除機能のデフォルトルーティング、アクションの登録
// 追加されたルートにURL:/articleでindexアクション：一覧表示があるが、↑の個別で作製したアクションと重複するので無効にする
Route::resource('/articles', 'ArticleController')->except(['index'])->middleware('auth');
// ↑MiddlewareのAuthによるルート制限を設定し、URL入力による未ログイン者のアクセスを防ぐ
// 上記以外の例えばindexアクションによる一覧画面は個別で作製したルートなので制限されない