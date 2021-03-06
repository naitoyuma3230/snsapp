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

// googleAPIの認証{}の部分はプロバイダー名（Google）
Route::prefix('login')->name('login.')->group(function () {
  Route::get('/{provider}', 'Auth\LoginController@redirectToProvider')->name('{provider}');

  // googleAPIからのリダイレクトは/login/google/callback
  // 応答が返ってきたらhandleProviderCallbackを実行
  Route::get('/{provider}/callback', 'Auth\LoginController@handleProviderCallback')->name('{provider}.callback');
});

// Google認証で新規登録を行うためのルーティング
// 登録済みユーザーの場合はログインしてホームへリダイレクト済み
Route::prefix('register')->name('register.')->group(function () {
  // Googleからの新規登録ページ
  Route::get('/{provider}', 'Auth\RegisterController@showProviderUserRegistrationForm')->name('{provider}');

  // 新規登録ページのPost先
  Route::post('/{provider}', 'Auth\RegisterController@registerProviderUser')->name('{provider}');
});


// 個別のルーティング、コントローラーの記述
// ルート名を追加したデフォルトのArticleに合わせる
Route::get('/', 'ArticleController@index')->name('articles.index');

// 一覧,個別表示,新規登録,更新,削除機能のデフォルトルーティング、アクションの登録
// ↑の個別で作製したアクションと重複するのでexceptメソッドで無効にする
// またMiddlewareのAuthによるルート制限を設定し、URL入力による未ログイン者のアクセスを防ぐ
Route::resource('/articles', 'ArticleController')->except(['index', 'show'])->middleware('auth');

// ↑で除いたshowアクションをAuthを適応せず使用するため、showのみ再び定義
Route::resource('/articles', 'ArticleController')->only(['show']);



// いいねボタンのルーティング
// prefixで引数をURLに付加しnameでURL名を'article/'に指定
// group()はそこまでの一連の処理を無名関数として、後述のput/deleteのlikeとunlikeメソッドの登録コードを短縮している

Route::prefix('articles')->name('articles.')->group(function () {
  // put/deleteメソッドで'article(prefix付加部分)/{article(記事id)/like}'へリクエストを送る事でlike/unlikeアクション
  Route::put('/{article}/like', 'ArticleController@like')->name('like')->middleware('auth');
  Route::delete('/{article}/like', 'ArticleController@unlike')->name('unlike')->middleware('auth');
});

// ルーティング上で{name}を渡す
Route::get('/tags/{name}', 'TagController@show')->name('tags.show');

Route::prefix('users')->name('users.')->group(function () {

  // "/users/{name}/...のプレフィックス
  Route::get('/{name}', 'UserController@show')->name('show');
  Route::get('/{name}/likes', 'UserController@likes')->name('likes');

  Route::get('/{name}/followings', 'UserController@followings')->name('followings');
  Route::get('/{name}/followers', 'UserController@followers')->name('followers');

  // "/user/{name}/...にAuth機能追加(ログイン時のみリクエストが通る)"
  Route::middleware('auth')->group(function () {
    Route::put('/{name}/follow', 'UserController@follow')->name('follow');
    Route::delete('/{name}/follow', 'UserController@unfollow')->name('unfollow');
  });
});
