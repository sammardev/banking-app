<?php

namespace App\Http\Controllers;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // Ensure the user has an account
        $account = Account::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        // Fetch all transactions (Deposits & Transfers) and paginate
        $transactions = Transaction::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest()
            ->paginate(10); // Show 10 transactions per page

        return view('dashboard', compact('account', 'transactions'));
    }

    public function deposit(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1', // Allow any deposit for existing users
                'deposit_source' => 'required|string|in:Card,Cheque,Online,Cash' // Validate deposit source
            ]);

            $user = Auth::user();
            $account = Account::firstOrCreate(
                ['user_id' => $user->id], 
                ['balance' => 0, 'reserved_balance' => 0]
            );

            // Check if user is new (has no deposits in transaction table)
            $isNewUser = !Transaction::where('sender_id', $user->id)->where('type', 'deposit')->exists();

            if ($isNewUser) {
                if ($request->amount < 50) {
                    return response()->json(['success' => false, 'message' => 'Minimum deposit must be $50 for new users.'], 400);
                }

                // Deduct $20 into reserved balance (only once for new users)
                $account->reserved_balance = 20;
                $request->amount -= 20; // Reduce $20 from the deposit (only for new users)
            }

            // Add remaining deposit amount to balance
            $account->balance += $request->amount;
            $account->save();

            // Ensure $20 is reserved for existing users if not already deducted
            if ($account->reserved_balance < 20) {
                $account->reserved_balance = 20;
                $account->save();
            }

            // Record the deposit transaction
            Transaction::create([
                'sender_id' => $user->id,
                'receiver_id' => null,
                'amount' => $request->amount + ($isNewUser ? 20 : 0), // Show full deposit amount
                'type' => 'deposit',
                'deposit_source' => $request->deposit_source
            ]);

            return response()->json([
                'success' => true,
                'new_balance' => $account->balance,
                'reserved_balance' => $account->reserved_balance,
                'usable_balance' => max(0, $account->balance - $account->reserved_balance)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

}