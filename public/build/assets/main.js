function openTransferModal() {
    document.getElementById('transferModal').classList.remove('hidden');
}

function closeTransferModal() {
    document.getElementById('transferModal').classList.add('hidden');
}

// Step 1: Validate Bank Selection
function validateBankSelection() {
    let bankName = document.getElementById('transferBank').value;

    if (bankName === "" || bankName === null) {
        Swal.fire({ icon: 'error', title: 'Select a Bank', text: 'Please select a bank before proceeding.' });
        return;
    }

    if (bankName !== "ABC Bank") {
        Swal.fire({ icon: 'error', title: 'Transfer Not Allowed', text: 'Cannot transfer to other banks.' });
        return;
    }

    document.getElementById('transferStep1').classList.add('hidden');
    document.getElementById('transferStep2').classList.remove('hidden');
}

// Step 2: Go Back to Bank Selection
function goBackToBankSelection() {
    document.getElementById('transferStep2').classList.add('hidden');
    document.getElementById('transferStep1').classList.remove('hidden');
}

// Step 3: Fetch Recipient Details
function fetchRecipientDetails() {
    let accountNumber = document.getElementById('transferAccountNumber').value.trim();

    if (!accountNumber || accountNumber.length !== 10) {
        Swal.fire({ icon: 'error', title: 'Invalid Account Number', text: 'Please enter a valid 10-digit account number.' });
        return;
    }

    fetch(`/api/get-user-by-account/${accountNumber}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('recipientAccount').innerText = accountNumber;
                document.getElementById('recipientName').innerText = data.name;
                document.getElementById('recipientEmail').innerText = data.email;
                document.getElementById('transferStep2').classList.add('hidden');
                document.getElementById('transferStep3').classList.remove('hidden');
            } else {
                Swal.fire({ icon: 'error', title: 'Account Not Found', text: 'No user found with this account number.' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Network Error', text: 'Please check your connection and try again.' });
        });
}

// Step 4: Go Back to Account Entry
function goBackToAccountEntry() {
    document.getElementById('transferStep3').classList.add('hidden');
    document.getElementById('transferStep2').classList.remove('hidden');
}

// Step 5: Submit Transfer
function submitTransfer() {
    let accountNumber = document.getElementById('transferAccountNumber').value.trim();
    let amount = document.getElementById('transferAmount').value.trim();
    let currentBalance = parseFloat(document.getElementById('currentBalance').innerText.replace('$', ''));
    let transferUrl = document.getElementById("transferForm").getAttribute("data-url");

    if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
        Swal.fire({ icon: 'error', title: 'Invalid Amount', text: 'Please enter a valid transfer amount.' });
        return;
    }

    if (parseFloat(amount) > (currentBalance - 0)) {
        Swal.fire({ icon: 'error', title: 'Insufficient Funds', text: 'You do not have enough usable balance for this transfer.' });
        return;
    }

    fetch(transferUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({ account_number: accountNumber, amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Transfer Successful', text: 'The money has been sent successfully.' }).then(() => {
                    document.getElementById('currentBalance').innerText = `$${parseFloat(data.new_balance).toFixed(2)}`;
                    closeTransferModal();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Transfer Failed', text: data.message || 'Something went wrong. Try again.' });
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire({ icon: 'error', title: 'Network Error', text: 'Failed to communicate with the server. Please try again.' });
        });
}



// deposit JS

function openDepositModal() {
    document.getElementById('depositModal').classList.remove('hidden');
}

function closeDepositModal() {
    document.getElementById('depositModal').classList.add('hidden');
}

function nextStep() {
    let amount = document.getElementById('depositAmount').value.trim();
    if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
        Swal.fire({ icon: 'error', title: 'Invalid Amount', text: 'Please enter a valid deposit amount.' });
        return;
    }
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
}

function validateAmount(input) {
    input.value = input.value.replace(/[^0-9.]/g, '');
}

function validateCardNumber(input) {
    input.value = input.value.replace(/\D/g, '').substring(0, 16);
}

function validateExpiry(input) {
    input.value = input.value.replace(/[^0-9\/]/g, '').substring(0, 5);
    if (input.value.length === 2 && !input.value.includes('/')) {
        input.value += '/';
    }
}

function validateCVC(input) {
    input.value = input.value.replace(/\D/g, '').substring(0, 3);
}

function openDepositModal() {
    document.getElementById('depositModal').classList.remove('hidden');
    document.getElementById('depositStep1').classList.remove('hidden'); // Show Step 1
    document.getElementById('depositStep2').classList.add('hidden'); // Hide Step 2
}

function closeDepositModal() {
    document.getElementById('depositModal').classList.add('hidden');
}

// 🚀 **Next Step (From Amount to Card Details)**
function nextDepositStep() {
    let amount = parseFloat(document.getElementById('depositAmount').value.trim());

    if (isNaN(amount) || amount < 1) {
        Swal.fire({ icon: 'error', title: 'Invalid Amount', text: 'Please enter a valid deposit amount.' });
        return;
    }

    // Ensure new users deposit at least $50
    let currentBalance = parseFloat(document.getElementById('currentBalance') ? .innerText.replace('$', '') || 0);
    if (currentBalance === 0 && amount < 50) {
        Swal.fire({ icon: 'error', title: 'Minimum Deposit Required', text: 'New users must deposit at least $50.' });
        return;
    }

    document.getElementById('depositStep1').classList.add('hidden');
    document.getElementById('depositStep2').classList.remove('hidden');
}

// 🔙 **Previous Step (From Card Details to Amount)**
function prevDepositStep() {
    document.getElementById('depositStep1').classList.remove('hidden');
    document.getElementById('depositStep2').classList.add('hidden');
}

// 🚀 **Submit Deposit**
function submitDeposit() {
    let amount = parseFloat(document.getElementById('depositAmount').value.trim());
    let depositSource = "Card"; // Default deposit method
    let cardNumber = document.getElementById('cardNumber').value.trim();
    let cardExpiry = document.getElementById('cardExpiry').value.trim();
    let cardCVC = document.getElementById('cardCVC').value.trim();

    let depositUrl = document.getElementById("depositForm") ? .getAttribute("data-url");

    if (!depositUrl) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Deposit URL is missing. Please refresh the page.' });
        return;
    }

    if (!cardNumber || cardNumber.length !== 16) {
        Swal.fire({ icon: 'error', title: 'Invalid Card Number', text: 'Card number must be 16 digits.' });
        return;
    }

    if (!cardExpiry || !/^\d{2}\/\d{2}$/.test(cardExpiry)) {
        Swal.fire({ icon: 'error', title: 'Invalid Expiry Date', text: 'Expiry must be in MM/YY format.' });
        return;
    }

    if (!cardCVC || cardCVC.length !== 3) {
        Swal.fire({ icon: 'error', title: 'Invalid CVC', text: 'CVC must be 3 digits.' });
        return;
    }

    console.log("Submitting deposit to:", depositUrl, { amount, deposit_source: depositSource });

    fetch(depositUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({ amount: amount, deposit_source: depositSource })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data }))) // Extract both status and body
        .then(({ status, body }) => {
            if (status === 422) { // Laravel validation error
                Swal.fire({ icon: 'error', title: 'Deposit Failed', text: body.message });
                return;
            }
            if (!body.success) { // Other server errors
                Swal.fire({ icon: 'error', title: 'Deposit Failed', text: body.message || 'Something went wrong. Try again.' });
                return;
            }

            // Success Message
            Swal.fire({
                icon: 'success',
                title: 'Deposit Successful',
                text: 'Your deposit has been processed.'
            }).then(() => {
                if (document.getElementById('currentBalance')) {
                    document.getElementById('currentBalance').innerText = `$${parseFloat(body.new_balance).toFixed(2)}`;
                }
                if (document.getElementById('reservedBalance')) {
                    document.getElementById('reservedBalance').innerText = `$${parseFloat(body.reserved_balance).toFixed(2)}`;
                }
                if (document.getElementById('usableBalance')) {
                    document.getElementById('usableBalance').innerText = `$${(parseFloat(body.new_balance) - parseFloat(body.reserved_balance)).toFixed(2)}`;
                }

                closeDepositModal();
            });
        })
        .catch(error => {
            console.error('Fetch error:', error);
            Swal.fire({ icon: 'error', title: 'Server Error', text: 'Something went wrong while processing your request.' });
        });
}





function copyAccountNumber() {
    let accountNumberElement = document.getElementById("accountNumber");
    let accountNumber = accountNumberElement.textContent.trim(); // Ensure no extra spaces

    let tempInput = document.createElement("input"); // Create a temporary input
    tempInput.value = accountNumber;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy"); // Copy text
    document.body.removeChild(tempInput); // Remove temporary input

    // Show SweetAlert2 success notification
    Swal.fire({
        icon: 'success',
        title: 'Copied!',
        text: `Account number ${accountNumber} copied to clipboard.`,
        timer: 2000, // Auto-close in 2 seconds
        showConfirmButton: false
    });
}
