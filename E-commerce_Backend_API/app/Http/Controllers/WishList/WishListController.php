<?php

namespace App\Http\Controllers\WishList;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\Product;
// use App\Models\User;
use App\Models\WishList;
use App\Models\User;

class WishListController extends Controller
{
    protected $user;
    public function __construct(){
        $this->user = new User();
    }

    public function store(Request $request){
        
        // $product = Product::findorFail($request->product_id);
        // $user = User::findorFail($request->user_id);
        // $product->wishlists()->create();
        // $user->wishlists()->create();

        // return "succesfull";

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = $this->user->where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        else{
            $exists = WishList::where('user_id', $user->id)->where('product_id', $request->product_id)->exists();

            if ($exists) {
                return response()->json(['message' => 'Already in wishlist'], 200);
            }
            else{
                $wishlist = WishList::create([
                    'user_id' => $user->id,
                    'product_id' => $request->product_id,
                ]);
        
                return response()->json(['message' => 'Added to wishlist successfully!', 'data' => $wishlist], 201);
            }
        }
    }

    public function index(Request $request){
        $user = $this->user->where('email', $request->email)->first();

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        else{
            return WishList::where('user_id', $user->id)->with(['user', 'product'])->get();
        }
    }

    public function destroy(Request $request){
        $user = $this->user->where('email', $request->email)->first();

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        else{
            $wishlist = WishList::where('user_id', $user->id)->delete();
            if($wishlist){
                return response()->json(['message' => 'Product delete from wishlist successfully'], 201);
            }
        }
    }
}
