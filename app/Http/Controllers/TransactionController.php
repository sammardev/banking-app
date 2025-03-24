<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function transfer(Request $request)
    {
        try {
            $request->validate([
                'account_number' => 'required|digits:10',
                'amount' => 'required|numeric|min:1'
            ]);

            $sender = Auth::user();
            $senderAccount = Account::where('user_id', $sender->id)->first();

            // Check if the sender's account exists
            if (!$senderAccount) {
                return response()->json(['success' => false, 'message' => 'Sender account not found.'], 400);
            }

            // Prevent self-transfer
            if ($senderAccount->account_number === $request->account_number) {
                return response()->json(['success' => false, 'message' => 'Self-transfer is not allowed.'], 400);
            }

            // Check if the sender has enough balance
            if ($senderAccount->balance < $request->amount) {
                return response()->json(['success' => false, 'message' => 'Insufficient balance.'], 400);
            }

            // Find the receiver account
            $receiverAccount = Account::where('account_number', $request->account_number)->first();
            
            // Check if the recipient account exists
            if (!$receiverAccount) {
                return response()->json(['success' => false, 'message' => 'Recipient account not found.'], 404);
            }

            // Deduct money from sender
            $senderAccount->balance -= $request->amount;
            $senderAccount->save();

            // Add money to receiver
            $receiverAccount->balance += $request->amount;
            $receiverAccount->save();

            // Create transaction
            Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiverAccount->user_id,
                'amount' => $request->amount,
                'type' => 'transfer',
            ]);

            return response()->json(['success' => true, 'new_balance' => $senderAccount->balance], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
