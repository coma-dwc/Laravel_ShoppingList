<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoppingListRegisterPostRequest;
use App\Models\Shopping_list as Shopping_listModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
  /**
   * トップページを表示する
   *
   * @return \Illiminate\View\View
   */
   public function list()
   {
     return view('shopping_list.list');
   }

   /**
     * 買うものの新規登録
     */
    public function register(ShoppingListRegisterPostRequest $request)
    {
      //validate済のデータの取得
      $datum = $request->validated();
      //var_dump($datum); exit;

      //user_idの追加
      $datum['user_id'] = Auth::id();

      //テーブルへのINSERT
      try {
          $r = Shopping_listModel::create($datum);
      } catch(\Throwable $e) {
        // XXX 本当はログに書く等の処理をする。今回は一端「出力する」だけ
        echo $e->getMessage();
        exit;
      }

      //買うもの登録成功
      $request->session()->flash('front.shoppinglist_register_success', true);

      //
      return redirect('/shopping_list/list');
    }
}