<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\User;
use App\Http\Requests\PaymentRegisterRequest;

class PaymentController extends Controller
{
    protected $user;
    public function __construct(){
        $this->user = new User();
    }

    public function store(PaymentRegisterRequest $request){

        $validateData = $request->validated();

        $user = $this->user->where('email', $validateData['email'])->where('mobile_number', $validateData['mobile_number'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        else{
            
            $payment = Payment::create([
                'price' => $validateData['price'],
                'transaction_id' => $validateData['transaction_id'],
                'user_id' => $user->id,
                'product_id' => $validateData['product_id'],
            ]);
        
            return response()->json(['message' => 'Payment successfully!', 'data' => $payment], 201);
        }
    }

    public function index(Request $request){
        $user = $this->user->where('email', $request->email)->first();

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        else{
            return Payment::where('user_id', $user->id)->with(['user', 'product'])->get();
        }
    }
}
