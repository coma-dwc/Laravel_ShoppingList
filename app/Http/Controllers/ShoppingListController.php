<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
}