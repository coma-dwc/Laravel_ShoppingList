<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShoppingListRegisterPostRequest;
use App\Models\Shopping_list as Shopping_listModel;
use App\Models\Completed_Shopping_list as Completed_Shopping_listModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

     //1ページ当たりの表示アイテム数を設定
     $per_page = 5;

     //一覧の取得
     $list = Shopping_listModel::where('user_id', Auth::id())
                              ->paginate($per_page);
                              //->get();
//$sql = Shopping_listModel::toSql();
//echo "<pre>\n"; var_dump($sql, $list); exit;
    //
     return view('shopping_list.list', ['list' => $list]);
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

    /**
     * 削除処理
     */
     public function delete(Request $request, $shopping_list_id)
     {
       //shopping_list_idのレコード取得
       $shopping_list = $this->getShopping_listModel($shopping_list_id);
       //タスクを削除する
       if ($shopping_list !== null) {
         $shopping_list->delete();
         $request->session()->flash('front.shoppinglist_delete_success', true);
       }
       //一覧に遷移
       return redirect('/shopping_list/list');
     }

     /**
      * 買うものの完了
      */
      public function complete(Request $request, $shopping_list_id)
      {
        /* 買うものを完了テーブルに移動 */
        try {
          DB::beginTransaction(); //トランザクション開始

          //shopping_list_idのレコード取得
          $shopping_list = $this->getShopping_listModel($shopping_list_id);
          if ($shopping_list === null) {
            throw new \Exception(''); //不正によるトランザクション終了
          }

          //shopping_lists側を削除
          $shopping_list->delete();
//var_dump($shopping_list->toArray()); exit;

          //completed_shopping_lists側にINSERT
          $dask_datum = $shopping_list->toArray();
          unset($dask_datum['created_at']);
          $r = Completed_Shopping_listModel::create($dask_datum);
          if ($r === null) {
            //insertで失敗したのでトランザクション終了
            throw new \Exception('');
          }

          //トランザクション終了
          DB::commit();

          //完了メッセージ
          $request->session()->flash('front.shoppinglist_completed_success', true);
        } catch(\Throwable $e) {
//var_dump($e->getMessage()); exit;
          //トランザクション異常終了
          DB::rollback();
          //失敗メッセージ
          $request->session()->flash('front.shoppinglist_completed_failure', true);
        }

        //一覧に遷移
        return redirect('/shopping_list/list');
      }
}