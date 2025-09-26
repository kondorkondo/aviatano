@extends('Layout.usergame')
@section('content')
    <div class="deposite-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="pay-tabs">
                        <a href="/deposit" class="custom-tabs-link">DEPOSIT</a>
                        <a href="#" class="custom-tabs-link active">WITHDRAW</a>
                    </div>

                    <input type="hidden" name="username" id="username" value="">
                    <input type="hidden" name="password" id="password" value="">

                    <div class="pay-options">
                        <div class="payment-cols">
                            <div class="grid-view">
                                <!-- Mobile Money Tanzania - Active Withdrawal Method -->
                                <div class="grid-list" onclick="withdrawalGatewayDetails('10')">
                                    <button class="btn payment-btn active" data-tab="mobile_money">
                                        <img src="images/app-logo/mobile-money-tz.svg" alt="Mobile Money Tanzania" />
                                        <div class="PaymentCard_limit">Min 1000 TSh</div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile Money Tanzania Withdrawal Section -->
                        <div class="deposite-box" id="mobile_money" style="display: block;">
                            <div class="d-box">
                                <div class="limit-txt">LIMITS:<span>1000 TSh - 50000 TSh</span></div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="input-group flex-nowrap mb-3">
                                            <span class="input-group-text" id="addon-wrapping">
                                                <span class="material-symbols-outlined">
                                                    payments
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" id="withdraw_amount" 
                                                   placeholder="Enter withdrawal amount in TSh" 
                                                   oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group flex-nowrap mb-3">
                                            <span class="input-group-text" id="addon-wrapping">
                                                <span class="material-symbols-outlined">
                                                    phone
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" id="withdraw_phone" 
                                                   placeholder="Enter your mobile number (e.g., 0744963858)"
                                                   value="{{ auth()->user()->phone ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="input-group flex-nowrap mb-3">
                                            <span class="input-group-text" id="addon-wrapping">
                                                <span class="material-symbols-outlined">
                                                    person
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" id="account_holder_name" 
                                                   placeholder="Account holder name"
                                                   value="{{ auth()->user()->name ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="amount-buttons">
                                            <button class="btn amount-tooltips-btn" onclick="selectWithdrawAmount(5000)">5000</button>
                                            <button class="btn amount-tooltips-btn" onclick="selectWithdrawAmount(10000)">10000</button>
                                            <button class="btn amount-tooltips-btn" onclick="selectWithdrawAmount(25000)">25000</button>
                                            <button class="btn amount-tooltips-btn" onclick="selectWithdrawAmount(50000)">50000</button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="current-balance">
                                            <div>AVAILABLE BALANCE</div>
                                            <div class="balance-value">TSh <span id="current_balance">{{ auth()->user()->balance ?? '0' }}</span></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="withdraw-blc">
                                            <div>BALANCE AFTER WITHDRAWAL</div>
                                            <div class="dopsite-vlue">TSh <span id="after_withdraw_balance">0</span></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow" onclick="processMobileMoneyWithdrawal()">
                                            WITHDRAW
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Withdrawal Instructions Section -->
                    <div class="pay-static-form text-white fw-bold">
                        <div class="form-back d-flex align-items-center" onclick="history.back()">
                            <span class="material-symbols-outlined bold-icon me-1">
                                arrow_back
                            </span>
                            BACK
                        </div>
                        
                        <div class="white-box mt-3">
                            <h5 class="text-muted f-14 fw-bold">WITHDRAWAL INFORMATION</h5>
                            <div class="withdrawal-info">
                                <div class="info-item">
                                    <span class="material-symbols-outlined">schedule</span>
                                    <div>
                                        <strong>Processing Time:</strong> 1-4 hours during business days
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="material-symbols-outlined">info</span>
                                    <div>
                                        <strong>Note:</strong> Ensure your account is verified before withdrawing
                                    </div>
                                </div>
                                <div class="info-item">
                                    <span class="material-symbols-outlined">security</span>
                                    <div>
                                        <strong>Security:</strong> Withdrawals only to registered Mobile Money numbers
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="white-box mt-3">
                            <h5 class="text-muted f-14 fw-bold">WITHDRAWAL DETAILS</h5>
                            <form action="/withdrawNow" method="post" id="withdraw_form">
                                @csrf
                                <input type="hidden" name="amount" id="hidden_withdraw_amount" value="">
                                <input type="hidden" name="payment_gateway_type" id="withdraw_payment_gateway_type" value="10">
                                <input type="hidden" name="mobile_no" id="hidden_mobile_no" value="">

                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold">
                                        Amount (TSh)
                                    </label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="final_amount">
                                                <input type="text" class="form-control text-indent-0" id="final_amount"
                                                    name="final_amount" readonly>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold">
                                        Mobile Number
                                    </label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="final_mobile">
                                                <input type="text" class="form-control text-indent-0" id="final_mobile"
                                                    name="final_mobile" readonly>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold">
                                        Account Name
                                    </label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="final_name">
                                                <input type="text" class="form-control text-indent-0" id="final_name"
                                                    name="final_name" readonly>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold">
                                        Transaction PIN
                                    </label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="transaction_pin">
                                                <input type="password" class="form-control text-indent-0" id="transaction_pin"
                                                    name="transaction_pin" placeholder="Enter your 4-digit PIN" maxlength="4">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="transaction_pin-error" class="error" for="transaction_pin"></label>
                                </div>

                                <button type="submit" class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow">
                                    CONFIRM WITHDRAWAL
                                </button>
                            </form>
                        </div>

                        <!-- Recent Withdrawals -->
                        <div class="white-box mt-3">
                            <h5 class="text-muted f-14 fw-bold">RECENT WITHDRAWALS</h5>
                            <div class="recent-transactions">
                                @if(isset($recentWithdrawals) && count($recentWithdrawals) > 0)
                                    @foreach($recentWithdrawals as $withdrawal)
                                        <div class="transaction-item">
                                            <div class="transaction-info">
                                                <span class="transaction-amount">TSh {{ number_format($withdrawal->amount) }}</span>
                                                <span class="transaction-date">{{ $withdrawal->created_at->format('M d, H:i') }}</span>
                                            </div>
                                            <div class="transaction-status status-{{ strtolower($withdrawal->status) }}">
                                                {{ $withdrawal->status }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="no-transactions">No recent withdrawals</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ url('user/withdraw.js') }}?v={{ time() }}"></script>
    
    <!-- Withdrawal JavaScript -->
    <script>
        // Current user balance
        const currentBalance = parseFloat("{{ auth()->user()->balance ?? 0 }}");
        
        // Amount selection function
        function selectWithdrawAmount(amount) {
            $('#withdraw_amount').val(amount);
            calculateAfterWithdrawBalance();
        }
        
        // Calculate balance after withdrawal
        function calculateAfterWithdrawBalance() {
            const withdrawAmount = parseFloat($('#withdraw_amount').val()) || 0;
            const afterWithdraw = currentBalance - withdrawAmount;
            $('#after_withdraw_balance').text(afterWithdraw.toLocaleString());
        }
        
        // Real-time calculation
        $(document).ready(function() {
            $('#withdraw_amount').on('input', function() {
                calculateAfterWithdrawBalance();
            });
            
            // Pre-fill form fields
            $('#withdraw_phone').on('change', function() {
                $('#hidden_mobile_no').val($(this).val());
                $('#final_mobile').val($(this).val());
            });
            
            $('#account_holder_name').on('change', function() {
                $('#final_name').val($(this).val());
            });
        });
        
        // Mobile Money withdrawal processing
        function processMobileMoneyWithdrawal() {
            const amount = parseFloat($("#withdraw_amount").val());
            const phone = $("#withdraw_phone").val();
            const accountName = $("#account_holder_name").val();
            
            // Validation
            if (!amount || amount < 1000) {
                alert("Minimum withdrawal amount is 1000 TSh");
                return;
            }
            
            if (amount > 50000) {
                alert("Maximum withdrawal amount is 50000 TSh");
                return;
            }
            
            if (amount > currentBalance) {
                alert("Insufficient balance");
                return;
            }
            
            if (!validateTanzaniaPhone(phone)) {
                alert("Please enter a valid Tanzanian mobile number (e.g., 0744963858)");
                return;
            }
            
            if (!accountName || accountName.trim().length < 2) {
                alert("Please enter account holder name");
                return;
            }
            
            // Update final form fields
            $('#final_amount').val(amount.toLocaleString());
            $('#final_mobile').val(phone);
            $('#final_name').val(accountName);
            $('#hidden_withdraw_amount').val(amount);
            $('#hidden_mobile_no').val(phone);
            
            // Scroll to confirmation form
            $('html, body').animate({
                scrollTop: $("#withdraw_form").offset().top - 100
            }, 500);
        }
        
        // Tanzania phone number validation
        function validateTanzaniaPhone(phone) {
            const regex = /^0[67][0-9]{8}$/;
            return regex.test(phone.replace(/\s+/g, ''));
        }
        
        // Form submission handling
        $(document).ready(function() {
            $('#withdraw_form').on('submit', function(e) {
                e.preventDefault();
                
                const pin = $('#transaction_pin').val();
                if (!pin || pin.length !== 4 || !/^\d+$/.test(pin)) {
                    alert("Please enter a valid 4-digit PIN");
                    return;
                }
                
                // Show loading state
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Processing...');
                
                // Submit form via AJAX
                $.ajax({
                    url: '/withdrawNow',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('✅ ' + response.message);
                            window.location.href = '/withdraw?msg=' + encodeURIComponent(response.message);
                        } else {
                            alert('❌ ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('❌ Withdrawal failed. Please try again.');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
        
        // Fallback function definition
        if (typeof processMobileMoneyWithdrawal === 'undefined') {
            window.processMobileMoneyWithdrawal = processMobileMoneyWithdrawal;
        }
    </script>
    
    <!-- Success message handling -->
    @isset($_GET['msg'])
        <script>
            toastr.success("{{ $_GET['msg'] }}")
        </script>
    @endisset
@endsection

<style>
.withdrawal-info .info-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.withdrawal-info .info-item span {
    margin-right: 10px;
    color: #007bff;
}

.current-balance {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    margin: 15px 0;
}

.balance-value {
    font-size: 24px;
    font-weight: bold;
    margin-top: 5px;
}

.recent-transactions .transaction-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.transaction-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-completed { background: #d4edda; color: #155724; }
.status-pending { background: #fff3cd; color: #856404; }
.status-failed { background: #f8d7da; color: #721c24; }

.no-transactions {
    text-align: center;
    color: #6c757d;
    padding: 20px;
}
</style>