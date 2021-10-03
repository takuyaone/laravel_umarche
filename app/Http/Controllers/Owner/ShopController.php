<?php

namespace App\Http\Controllers\owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use Illuminate\Support\Facades\Storage;
use InterventionImage;
use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;
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
        // phpinfo();
        // $ownerId=Auth::id();
        $shops=Shop::where('owner_id',Auth::id())->get();
        return view('owner.shops.index',compact('shops'));
    }

    public function edit($id)
    {
        $shop=Shop::findOrFail($id);

        return view('owner.shops.edit',compact('shop'));
    }

    public function update(UploadImageRequest $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'information' => 'required|string|max:1000',
            'is_selling' => 'required',
        ]);

        $imageFile=$request->image;
        if(!is_null($imageFile) && $imageFile->isValid()){
            $fileNameToStore=ImageService::upload($imageFile,'shops');
        }

        $shop = Shop::findOrFail($id);
        $shop->name = $request->name;
        $shop->information=$request->information;
        $shop->is_selling=$request->is_selling;
        if (!is_null($imageFile) && $imageFile->isValid()) {
            $shop->filename= $fileNameToStore;
        }
        $shop->save();


        return redirect()
        ->route('owner.shops.index')
        ->with(['message' => '店舗情報を更新しました。', 'status' => 'info']);
    }


}
