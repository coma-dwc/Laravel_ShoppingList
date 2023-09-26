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
   * 買うものページを表示する
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
       $shopping_list = Shopping_listModel::find($shopping_list_id);
       //↑もともとの記述
       /*$shopping_list = $this->getShopping_listModel($shopping_list_id); */

       //買うものを削除する
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
        /* タスクを完了テーブルに移動させる */
        try {
            // トランザクション開始
            DB::beginTransaction();

            // Shopping_list_idのレコードを取得する
            /*$shopping_list = $this->getShopping_listModel($shopping_list_id); */
            $shopping_list = Shopping_listModel::find($shopping_list_id);
            if ($shopping_list === null) {
                // $shopping_list_idが不正なのでトランザクション終了
                throw new \Exception('');
            }
//var_dump($shopping_list->toArray()); exit;
            // shopping_list側を削除する
            $shopping_list->delete();

            // completed_shopping_list側にinsertする
            $dask_datum = $shopping_list->toArray();
            unset($dask_datum['created_at']);
            unset($dask_datum['updated_at']);
            $r = Completed_Shopping_listModel::create($dask_datum);
            if ($r === null) {
                // insertで失敗したのでトランザクション終了
                throw new \Exception('');
            }
//echo '処理成功'; exit;

            // トランザクション終了
            DB::commit();
            // 完了メッセージ出力
            $request->session()->flash('front.shoppinglist_completed_success', true);
        } catch(\Throwable $e) {
var_dump($e->getMessage()); exit;
            // トランザクション異常終了
            DB::rollBack();
            // 完了失敗メッセージ出力
            $request->session()->flash('front.shoppinglist_completed_failure', true);
        }

        // 一覧に遷移する
        return redirect('/shopping_list/list');
    }
}