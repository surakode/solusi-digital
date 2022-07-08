<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Cart;
use App\Models\Item;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $idUser = Auth::user()->id;

        $items = DB::table('items')->paginate(10);

        $carts = DB::table('carts as a')
            ->leftJoin('items as b', 'a.items_id','=','b.id')
            ->select('a.*','b.price','b.name')
            ->where('a.users_id',$idUser)
            ->get();

        $sumCart = $carts->sum('subtotal');
        // dump($sumCarts);

        // dump($carts);

        $var = [
            'nav' => 'dashboard',
            'subNav' => 'dashboard',
            'title' => 'Barang',
            'items' => $items,
            'carts' => $carts,
            'sumCart' => $sumCart,
        ];

        if (Helper::checkACL('dashboard', 'r')) {

            return view('home.dashboard', $var);
        } else {
            return view('home.index', $var);
        }
    }

    public function create()
    {
        $var = [
            'nav' => 'dashboard',
            'subNav' => 'dashboard',
            'title' => 'Tambah Barang',
        ];

        return view('home.create', $var);
    }

    public function store(Request $request)
    {
        try {
            $vMessage = config('global.vMessage'); //get global validation messages
            $validator = Validator::make($request->all(), [
                'name'      => 'required',
                'price'     => 'required|integer',
                'stock'     => 'required|integer',
                'picture'     => 'required|mimes:jpeg,jpg,png|required|max:1024'
            ], $vMessage);
            // Valid?
            $valid = Helper::validationFail($validator);
            if(!is_null($valid)){
                return response()->json($valid); //return if not valid
            }

            $lastNumber = DB::table('items')->orderBy('id','DESC')->first();

            if ($lastNumber == null || empty($lastNumber)) {
                $number = 1;
            }
            else {
                $number = $lastNumber->id + 1;
            }

            $code = Helper::generatedCode($request->name,$number);

            $item = new Item;
            $item->name = $request->name;
            $item->price = $request->price;
            $item->code = $code;
            $item->stock = $request->stock;

            if ($request->hasFile('picture')) {
                // Mengambil file yang diupload
                $uploaded_cover = $request->file('picture');

                // mengambil extension file
                $extension = $uploaded_cover->getClientOriginalExtension();

                // membuat nama file random berikut extension
                $filename = md5(time()) . '.' . $extension;

                // menyimpan cover ke folder public/img
                $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
                $uploaded_cover->move($destinationPath, $filename);

                // mengisi field cover di book dengan filename yang baru dibuat
                $item->picture = $filename;
            }

            $item->save();
            $result = config('global.success.S002');

        }
        catch (\Throwable $e) {
            // dd($e);
            $result = config('global.errors.E009');
        }

        return response()->json($result);
    }

    public function addCart(Request $request)
    {
        $idItem = $request->id;
        $idUser = Auth::user()->id;

        $item =  DB::table('items')->where('id',$idItem)->first();

        $checkCart = DB::table('carts')
            ->where('users_id','=',$idUser)
            ->where('items_id','=',$idItem)
            ->first();

        try {
            if (empty($checkCart)) {
                $data = new Cart;
                $data->qty = 1;
                $data->subtotal = $item->price;
                $data->users_id = $idUser;
                $data->items_id = $idItem;
                $data->save();
            }
            else {
                $data = Cart::find($checkCart->id);
                $data->qty = $data->qty + 1;

                $qty = $data->qty * $item->price;
                $data->subtotal = $qty;
                $data->users_id = $idUser;
                $data->items_id = $idItem;
                $data->save();
            }

            $result = config('global.success.S001');

        }
        catch (QueryException $e) {
            $result = config('global.errors.E009');
        }
        catch (Throwable $th) {
            $result = config('global.errors.E009');
        }

        return response()->json($result);

    }

    public function deleteCart(Request $request)
    {
        $cart = Cart::find($request->id);

        if (!$cart->delete()) {
            $result = config('global.errors.E009');
        }
        else {
            $result = config('global.success.S003');
        }

        return response()->json($result);
    }
}
