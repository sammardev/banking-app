<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>
<?php if(session('success')): ?>
    <script> showSuccessAlert("<?php echo e(session('success')); ?>"); </script>
<?php endif; ?>
<?php if(session('error')): ?>
    <script> showErrorAlert("<?php echo e(session('error')); ?>"); </script>
<?php endif; ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <div class="flex justify-between items-center">
    <h3 class="text-lg font-bold">Your Current Useable Balance: <span id="currentBalance">$<?php echo e(number_format($account->balance ?? 0, 2)); ?></span></h3>
</div>

<div class="flex justify-between mt-2">
    <h4 class="text-gray-700 font-semibold">Total Balance:</h4>
    <span class="text-green-600 font-bold">$<?php echo e(number_format(max(0, $account->balance + $account->reserved_balance), 2)); ?></span>
</div>

<div class="flex justify-between mt-2">
    <h4 class="text-gray-700 font-semibold">Reserved Balance:</h4>
    <span class="text-red-600 font-bold">$<?php echo e(number_format($account->reserved_balance ?? 0, 2)); ?></span>
</div>

                    
                    
                <!-- Account Number Display with Copy Button -->
                <div class="flex items-center space-x-2">
                    <span class="text-gray-600 font-semibold">Account No:</span>
                    <span id="accountNumber" class="text-blue-600 font-bold"><?php echo e($account->account_number); ?></span>
                    <button onclick="copyAccountNumber()" class="bg-gray-200 p-1 rounded text-sm">📋 Copy</button>
                </div>
                </div>




<!-- Deposit Money Button -->
<button onclick="openDepositModal()" class="bg-green-500 text-white p-3 rounded flex items-center">
    💳 Deposit Money
</button>

<!-- Deposit Modal -->
<div id="depositModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-96">
        <h2 class="text-xl font-bold">Deposit Money</h2>

        <!-- Step 1: Enter Amount -->
        <div id="depositStep1">
            <form id="depositForm" data-url="<?php echo e(route('deposit')); ?>">
                <input type="text" id="depositAmount" placeholder="Enter Amount" class="border p-2 w-full mt-2" oninput="validateAmount(this)">
                <button type="button" onclick="nextDepositStep()" class="bg-blue-500 text-white p-2 w-full mt-2">Next</button>
            </form>
        </div>

        <!-- Step 2: Enter Card Details -->
        <div id="depositStep2" class="hidden">
            <h3 class="font-semibold">Enter Card Details</h3>
            <input type="text" id="cardNumber" placeholder="Card Number (16 digits)" class="border p-2 w-full mt-2" oninput="validateCardNumber(this)">
            <input type="text" id="cardExpiry" placeholder="MM/YY" class="border p-2 w-full mt-2" oninput="validateExpiry(this)">
            <input type="text" id="cardCVC" placeholder="CVC (3 digits)" class="border p-2 w-full mt-2" oninput="validateCVC(this)">
            
            <!-- Buttons for Navigation -->
            <div class="flex justify-between mt-2">
                <button type="button" onclick="prevDepositStep()" class="bg-gray-500 text-white p-2 w-1/3">Back</button>
                <button type="button" onclick="submitDeposit()" class="bg-green-500 text-white p-2 w-2/3">Deposit</button>
            </div>
        </div>

        <button onclick="closeDepositModal()" class="bg-red-500 text-white p-2 w-full mt-2">Cancel</button>
    </div>
</div>




           <!-- Transfer Money Button -->
<button onclick="openTransferModal()" class="bg-blue-500 text-white p-3 rounded flex items-center mt-4">
    🔄 Transfer Money
</button>

<!-- Transfer Modal -->
<div id="transferModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-96">
        <h2 class="text-xl font-bold">Transfer Money</h2>

        <!-- Step 1: Select Bank -->
        <div id="transferStep1">
            <label class="font-semibold">Select Bank:</label>
            <select id="transferBank" class="border p-2 w-full mt-2">
                <option value="">-- Select Bank --</option>
                <option value="ABC Bank">ABC Bank</option>
                <option value="DEF Bank">DEF Bank</option>
                <option value="GHI Bank">GHI Bank</option>
            </select>
            <button type="button" onclick="validateBankSelection()" class="bg-blue-500 text-white p-2 w-full mt-2">Next</button>
        </div>

        <!-- Step 2: Enter Account Number -->
        <div id="transferStep2" class="hidden">
            <input type="text" id="transferAccountNumber" placeholder="Enter Account Number" class="border p-2 w-full mt-2">
            <button type="button" onclick="fetchRecipientDetails()" class="bg-blue-500 text-white p-2 w-full mt-2">Next</button>
            <button type="button" onclick="goBackToBankSelection()" class="bg-gray-500 text-white p-2 w-full mt-2">Back</button>
        </div>

        <!-- Step 3: Show Recipient Details & Enter Amount -->
        <div id="transferStep3" class="hidden">
            <h3 class="font-semibold">Recipient Details</h3>
            <div class="p-2 bg-gray-100 rounded mt-2">
                <p><b>Bank Name</b> <span>ABC Bank</span></p>
                <p><b>Account No:</b> <span id="recipientAccount"></span></p>
                <p><b>Name:</b> <span id="recipientName"></span></p>
                <p><b>Email:</b> <span id="recipientEmail"></span></p>
            </div>

            <h3 class="font-semibold mt-3">Enter Amount</h3>
             <form id="transferForm" data-url="<?php echo e(route('transfer')); ?>">
                <input type="number" id="transferAmount" placeholder="Enter Amount" class="border p-2 w-full mt-2">
                <button type="button" onclick="submitTransfer()" class="bg-green-500 text-white p-2 w-full mt-2">Transfer</button>
                <button type="button" onclick="goBackToAccountEntry()" class="bg-gray-500 text-white p-2 w-full mt-2">Back</button>
            </form>
        </div>

        <button onclick="closeTransferModal()" class="bg-red-500 text-white p-2 w-full mt-2">Cancel</button>
    </div>
</div>





                <!-- Display Success/Error Messages -->
                <?php if(session('success')): ?>
                    <p class="text-green-500 mt-2"><?php echo e(session('success')); ?></p>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <p class="text-red-500 mt-2"><?php echo e(session('error')); ?></p>
                <?php endif; ?>

                <!-- Transaction History Table -->
<h4 class="mt-6 font-semibold">Transaction History</h4>
<table id="transactionTable" class="table-auto w-full mt-2 border">
    <thead>
        <tr>
            <th class="border px-4 py-2">Date</th>
            <th class="border px-4 py-2">Type</th>
            <th class="border px-4 py-2">Amount</th>
            <th class="border px-4 py-2">Status</th>
            <th class="border px-4 py-2">Details</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="border px-4 py-2"><?php echo e($transaction->created_at->format('Y-m-d H:i')); ?></td>
                <td class="border px-4 py-2"><?php echo e(ucfirst($transaction->type)); ?></td>
                <td class="border px-4 py-2">
                    <?php if($transaction->type == 'transfer' && $transaction->sender_id == Auth::id()): ?>
                        <span class="text-red-500">- $<?php echo e(number_format($transaction->amount, 2)); ?></span>
                    <?php else: ?>
                        <span class="text-green-500">+ $<?php echo e(number_format($transaction->amount, 2)); ?></span>
                    <?php endif; ?>
                </td>
                <td class="border px-4 py-2">
                    <?php if($transaction->type == 'transfer' && $transaction->sender_id == Auth::id()): ?>
                        <span class="text-red-500">Sent</span>
                    <?php elseif($transaction->receiver_id == Auth::id()): ?>
                        <span class="text-green-500">Received</span>
                    <?php else: ?>
                        <span class="text-green-500">Deposit</span>
                    <?php endif; ?>
                </td>
                <td class="border px-4 py-2">
                    <?php if($transaction->type == 'deposit'): ?>
                        <span>Source: <?php echo e($transaction->deposit_source); ?></span>
                    <?php else: ?>
                        <span>To: <?php echo e($transaction->receiver->name); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

                <?php if(count($transactions) == 0): ?>
                    <p class="text-gray-500 mt-2">No transactions yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>




 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\banking-app\resources\views/dashboard.blade.php ENDPATH**/ ?>