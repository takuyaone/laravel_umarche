<?php

namespace App\Http\Controllers\owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        $this->middleware(function ($request, $next) {
            // dd($request->route()->parameter('shop'));//文字列
            // dd(Auth::id());//数字
            $id=$request->route()->parameter('shop');//shopのidを取得
            if(!is_null($id)){//null判定
                $shopOwnerId=Shop::findOrFail($id)->owner->id;
                $shopId=(int)$shopOwnerId;//キャスト 文字列→数値に変換
                $owner=Auth::id();
                if($shopId !== $owner){//同じでなかったら
                    abort(404);//404画面表示
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        // $ownerId=Auth::id();
        $shops=Shop::where('owner_id',Auth::id())->get();
        return view('owner.shops.index',compact('shops'));
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }


}
