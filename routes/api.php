<?php

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get-user-by-account/{account_number}', function ($account_number) {
    $account = Account::where('account_number', $account_number)->first();

    if ($account) {
        return response()->json([
            'success' => true,
            'name' => $account->user->name,
            'email' => $account->user->email
        ]);
    } else {
        return response()->json(['success' => false, 'message' => 'Account not found.'], 404);
    }
});
