<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginPostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
  /**
   * * トップページを表示する
   *
   * @return \Illuminate\View\View
   */
   public function index()
   {
     return view('index');
   }

   /**
    * ログイン処理
    */
    public function login(LoginPostRequest $request)
    {
      //validate済

      //データの取得
      $datum = $request->validated();
      //var_dump($datum); exit;

      //認証
      if (Auth::attempt($datum) === false) {
        return back()
              ->withInput() //入力値の保持
              ;
      }

      //
      $request->session()->regenerate();
      return redirect()->intended('/shopping_list/list');
    }

    /**
     * ログアウト処理
     *
     */
     public function logout(Request $request)
     {
       Auth::logout();
       $request->session()->regenerateToken();
       $request->session()->regenerate();
       return redirect('/');
     }
}