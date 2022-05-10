<?php

namespace App\Http\Controllers;

use App\Article;

// 作製したArticleRequestクラスをインポート
use App\Http\Requests\ArticleRequest;


use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        // ダミーデータ
        // データベースから読み込む際、オブジェクト型で受け取るためそれに合わせる
        // （object）は型宣言、型キャスト
        $articles = [
            (object) [
                'id' => 1,
                'title' => 'タイトル1',
                'body' => '本文1',
                'created_at' => now(),
                'user' => (object) [
                    'id' => 1,
                    'name' => 'ユーザー名1',
                ],
            ],
            (object) [
                'id' => 2,
                'title' => 'タイトル2',
                'body' => '本文2',
                'created_at' => now(),
                'user' => (object) [
                    'id' => 2,
                    'name' => 'ユーザー名2',
                ],
            ],
            (object) [
                'id' => 3,
                'title' => 'タイトル3',
                'body' => '本文3',
                'created_at' => now(),
                'user' => (object) [
                    'id' => 3,
                    'name' => 'ユーザー名3',
                ],
            ],
        ];

        $articles = Article::all()->sortByDesc('created_at');

        return view('articles.index', ['articles' => $articles]);
    }

    public function create()
    {
        return view('articles.create');
    }

    // 引数の型宣言：$requestはArticlesRequestクラスのインスタンス、$articleはArticleクラスのインスタンスを指定している。
    // ->引数の型を限定することでメソッドの意図しない動作を防ぐ

    // laravelコントローラーでは引数の型宣言を行うと、メソッドの外でインスタンスが自動生成される
    // メソッドを呼び出す側が引数を指定しなくても、インスタンスが渡せれる
    // そのためこの場合Articleの型宣言を行わない場合メソッドの中でインスタンス化する必要がある。
    // この状態はメソッドがクラスに依存していると表現され、テストや変更がしやすい。(メソッドの書き換えが必要ないから)
    public function store(ArticleRequest $request, Article $article)
    {
        $article->title = $request->title;
        $article->body = $request->body;

        // ArticleモデルとUserモデルは紐づけ済みであるため->user()でアクセルしidを取得している
        $article->user_id = $request->user()->id;
        $article->save();
        return redirect()->route('articles.index');
    }
}