@extends('Layout.usergame')
@section('content')
    <div class="deposite-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="pay-tabs">
                        <a href="#" class="custom-tabs-link active">DEPOSIT</a>
                        <a href="/withdraw" class="custom-tabs-link">WITHDRAW</a>
                    </div>

                    <input type="hidden" name="username" id="username" value="">
                    <input type="hidden" name="password" id="password" value="">

                    <div class="pay-options">
                        <div class="payment-cols">
                            <div class="grid-view">
                                {{-- Commented out other payment methods - only mobile money available --}}
                                {{-- <div class="grid-list" onclick="paymentGatewayDetails('6')">
                                    <button class="btn payment-btn" data-tab="netbanking">
                                        <img src="images/app-logo/interkassa_net_banking.svg" />
                                        <div class="PaymentCard_limit">Min {{setting('min_recharge')}}</div>
                                    </button>
                                </div>
                                <div class="grid-list" onclick="paymentGatewayDetails('3')">
                                    <button class="btn payment-btn" data-tab="upi">
                                        <img src="images/app-logo/upiMt.svg" />
                                        <div class="PaymentCard_limit">Min {{setting('min_recharge')}}</div>
                                    </button>
                                </div> --}}
                                
                                {{-- Mobile Money Tanzania - Active Payment Method --}}
                                <div class="grid-list" onclick="paymentGatewayDetails('10')">
                                    <button class="btn payment-btn active" data-tab="mobile_money">
                                        <img src="images/app-logo/mobile-money-tz.svg" alt="Mobile Money Tanzania" />
                                        <div class="PaymentCard_limit">Min 100 TSh</div>
                                    </button>
                                </div>
                            </div>
                            {{-- Commented out netbanking payment form --}}
                            {{-- <div class="deposite-box" id="netbanking">
                                <div class="d-box">
                                    <div class="limit-txt">LIMITS:<span>{{setting('min_recharge')}}</span></div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="login-controls mt-3 rounded-pill h42">
                                                <label for="Username" class="rounded-pill">
                                                    <input type="text" class="form-control text-i10 amount"
                                                        id="net_bank_amount"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                                    <input type="hidden" id="net_bank_min_amount" value="{{setting('min_recharge')}}">
                                                    <input type="hidden" id="net_bank_max_amount" value="">
                                                    <i class="Input_currency">
                                                        INR
                                                    </i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <button
                                                class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow"
                                                onclick="deposit('6')">
                                                DEPOSIT
                                            </button>
                                        </div>
                                    </div>
                                    <div class="amount-tooltips">
                                        <button class="btn amount-tooltips-btn">500</button>
                                        <button class="btn amount-tooltips-btn active">1000</button>
                                        <button class="btn amount-tooltips-btn">2500</button>
                                        <button class="btn amount-tooltips-btn">5000</button>
                                    </div>
                                    <label for="net_bank_amount" class="error" id="net_bank_amount-error"></label>
                                </div>
                            </div> --}}
                            {{-- Commented out Phonepay payment form --}}
                            {{-- <div class="deposite-box" id="Phonepay">
                                <div class="d-box">
                                    <div class="limit-txt">LIMITS:<span>{{setting('min_recharge')}} - </span></div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="login-controls mt-3 rounded-pill h42">
                                                <label for="Username" class="rounded-pill">
                                                    <input type="text" class="form-control text-i10 amount"
                                                        id="phonepe_amount"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                                    <input type="hidden" id="phonepe_min_amount" value="{{setting('min_recharge')}}">
                                                    <input type="hidden" id="phonepe_max_amount" value="">
                                                    <i class="Input_currency">
                                                        INR
                                                    </i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <button
                                                class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow"
                                                onclick="deposit('2')">
                                                DEPOSIT
                                            </button>
                                        </div>
                                    </div>
                                    <div class="amount-tooltips">
                                        <button class="btn amount-tooltips-btn">500</button>
                                        <button class="btn amount-tooltips-btn active">1000</button>
                                        <button class="btn amount-tooltips-btn">5000</button>
                                        <button class="btn amount-tooltips-btn">10000</button>
                                    </div>
                                    <label for="phonepe_amount" class="error" id="phonepe_amount-error"></label>
                                </div>
                            </div> --}}
                            {{-- Commented out UPI payment form --}}
                            {{-- <div class="deposite-box" id="upi">
                                <div class="d-box">
                                    <div class="limit-txt">LIMITS:<span>{{setting('min_recharge')}}</span></div>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="login-controls mt-3 rounded-pill h42">
                                                <label for="Username" class="rounded-pill">
                                                    <input type="text" class="form-control text-i10 amount"
                                                        id="upi_amount"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
                                                    <input type="hidden" id="upi_min_amount" value="{{setting('min_recharge')}}">
                                                    <input type="hidden" id="upi_max_amount" value="">
                                                    <i class="Input_currency">
                                                        INR
                                                    </i>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <button
                                                class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow"
                                                onclick="deposit('3')">
                                                DEPOSIT
                                            </button>
                                        </div>
                                    </div>
                                    <div class="amount-tooltips">
                                        <button class="btn amount-tooltips-btn">500</button>
                                        <button class="btn amount-tooltips-btn active">1000</button>
                                        <button class="btn amount-tooltips-btn">2500</button>
                                        <button class="btn amount-tooltips-btn">5000</button>
                                    </div>
                                    <label for="upi_amount" class="error" id="upi_amount-error"></label>
                                    <div class="deposite-blc">
                                        <div>BALANCE AFTER DEPOSITING</div>
                                        <div class="dopsite-vlue">TSh <span id="upi_amount_txt"></span></div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        </div>
                        
                        <!-- Mobile Money Tanzania Section -->
                        <div class="deposite-box" id="mobile_money" style="display: block;">
                            <div class="d-box">
                                <div class="limit-txt">LIMITS:<span>1000 TSh - 100000 TSh</span></div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="input-group flex-nowrap mb-3">
                                            <span class="input-group-text" id="addon-wrapping">
                                                <span class="material-symbols-outlined">
                                                    payments
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" id="mobile_money_amount" 
                                                   placeholder="Enter amount in TSh" 
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
                                            <input type="text" class="form-control" id="mobile_money_phone" 
                                                   placeholder="Enter your mobile number (e.g., 0744963858)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="amount-buttons">
                                            <button class="btn amount-tooltips-btn" onclick="selectAmount(5000)">5000</button>
                                            <button class="btn amount-tooltips-btn" onclick="selectAmount(10000)">10000</button>
                                            <button class="btn amount-tooltips-btn" onclick="selectAmount(25000)">25000</button>
                                            <button class="btn amount-tooltips-btn" onclick="selectAmount(50000)">50000</button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="deposite-blc">
                                            <div>BALANCE AFTER DEPOSITING</div>
                                            <div class="dopsite-vlue">TSh <span id="mobile_money_amount_txt"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow" onclick="processMobileMoneyDeposit()">
                                            DEPOSIT
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    <div class="pay-static-form text-white fw-bold">
                        <div class="form-back d-flex align-items-center">
                            <span class="material-symbols-outlined bold-icon me-1">
                                arrow_back
                            </span>
                            BACK
                        </div>
                        <div class="white-box mt-3 text-center">
                            <img src="images/barcode.png" class="barcode-img" id="barcode"/>
                            <a href="#" class="d-block link-text">How to make deposit?</a>
                            <p class="text-dark">To confirm the deposit, make a transfer to the banking details:</p>
                            <div id="account_number_tag">
                                <div class="d-flex justify-content-between flex-wrap text-dark align-items-center">
                                    <span class="text-muted" id="account_number_title">ACCOUNT NUMBER : </span>
                                    <span class="d-flex align-items-center copy_owner_details" id="copy_acc_no">
                                        <span class="material-symbols-outlined bold-icon text-muted">
                                            content_copy
                                        </span>
                                        <span id="owner_account_number"></span>
                                        <input type="hidden" id="acc_no_hide">
                                    </span>
                                </div>
                            </div>
                            <div id="mobile_number_tag">
                                <div class="d-flex justify-content-between flex-wrap text-dark align-items-center my-2 ">
                                    <span class="text-muted" id="mobile_number_title"></span>
                                    <span class="d-flex align-items-center copy_owner_details" id="copy_mobile_no">
                                        <span class="material-symbols-outlined bold-icon text-muted">
                                            content_copy
                                        </span>
                                        <span id="owner_mobile_no"></span>
                                        <input type="hidden" id="mobile_no_hide">
                                    </span>
                                </div>
                            </div>
                            <div id="name_tag">
                                <div class="d-flex justify-content-between flex-wrap text-dark align-items-center">
                                    <span class="text-muted" id="account_name_title"></span>
                                    <span class="d-flex align-items-center copy_owner_details" id="copy_name">
                                        <span class="material-symbols-outlined bold-icon text-muted">
                                            content_copy
                                        </span>
                                        <span id="owner_name"></span>
                                        <input type="hidden" id="name_hide">
                                    </span>
                                </div>
                            </div>
                            <div id="bank_name_tag">
                                <div class="d-flex justify-content-between flex-wrap text-dark align-items-center my-2">
                                    <span class="text-muted" id="bank_title">BANK NAME:</span>
                                    <span class="d-flex align-items-center copy_owner_details" id="copy_bank_name">
                                        <span class="material-symbols-outlined bold-icon text-muted">
                                            content_copy
                                        </span>
                                        <span id="owner_bank_name"></span>
                                        <input type="hidden" id="bank_name_hide">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="white-box mt-3">
                            <h5 class="text-muted f-14 fw-bold">TO BE CREDITED</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="dopsite-vlue fw-bold f-20">
                                    <div>TSh <span id="select_amount"></span></div>
                                </div>
                                <button class="btn btn-transparent p-0">
                                    <span class="material-symbols-outlined bold-icon">
                                        edit
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="white-box mt-3">
                            <form action="/depositNow" method="post" id="deposit_form">
                                @csrf
                                <input type="hidden" name="amount" id="deposit_amount" value="300">
                                <input type="hidden" name="payment_gateway_type" id="payment_gateway_type">
                                <input type="hidden" name="min_deposit_amount" id="min_deposit_amount">
                                <input type="hidden" name="max_deposit_amount" id="max_deposit_amount">

                                <div class="mb-3 row" id="mobile_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="mobile_title"></label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="mobile_no">
                                                <input type="text" class="form-control text-indent-0" id="mobile_no"
                                                    name="mobile_no">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="mobile_no-error" class="error" for="mobile_no"></label>
                                </div>
                                <div class="mb-3 row" id="name_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="name_title"></label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="name">
                                                <input type="text" class="form-control text-indent-0" id="name"
                                                    name="name">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="name-error" class="error" for="name"></label>
                                </div>
                                <div class="mb-3 row" id="email_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="email_title"></label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="email">
                                                <input type="email" class="form-control text-indent-0" id="email"
                                                    name="email">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="email-error" class="error" for="email"></label>
                                </div>
                                <div class="mb-3 row" id="trn_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="">Transaction No.</label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="trn">
                                                <input type="text" class="form-control text-indent-0" id="trn"
                                                    name="trn" required>
                                            </label>
                                        </div>
                                    </div>
                                    <label id="email-error" class="error" for="email"></label>
                                </div>
                                <div class="mb-3 row" id="cwallet_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="cwallet_title"></label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="crypto_wallet_address">
                                                <input type="text" class="form-control text-indent-0"
                                                    id="crypto_wallet_address" name="crypto_wallet_address">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="crypto_wallet_address-error" class="error"
                                        for="crypto_wallet_address"></label>
                                </div>
                                <div class="mb-3 row" id="ctxt_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="ctxt_title"></label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="crypto_transaction_id">
                                                <input type="text" class="form-control text-indent-0"
                                                    id="crypto_transaction_id" name="crypto_transaction_id">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="crypto_transaction_id-error" class="error"
                                        for="crypto_transaction_id"></label>
                                </div>
                                <div class="mb-3 row" id="account_no_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="account_no_title">Account Number</label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="account_no_id">
                                                <input type="text" class="form-control text-indent-0" id="account_no"
                                                    name="account_no">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="account_no-error" class="error" for="account_no"></label>
                                </div>
                                <div class="mb-3 row" id="account_holder_name_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="account_holder_name_title">Account Holder Name</label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="account_holder_name_id">
                                                <input type="text" class="form-control text-indent-0"
                                                    id="account_holder_name" name="account_holder_name">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="account_holder_name-error" class="error"
                                        for="account_holder_name"></label>
                                </div>
                                <div class="mb-3 row" id="ifsc_code_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="ifsc_code_title">IFSC Code</label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="ifsc_code_id">
                                                <input type="text" class="form-control text-indent-0" id="ifsc_code"
                                                    name="ifsc_code">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="ifsc_code-error" class="error" for="ifsc_code"></label>
                                </div>
                                <div class="mb-3 row" id="bank_name_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="bank_name_title">Bank Name</label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="bank_name_id">
                                                <input type="text" class="form-control text-indent-0" id="bank_name "
                                                    name="bank_name">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="bank_name -error" class="error" for="bank_name"></label>
                                </div>
                                <div class="mb-3 row" id="upi_div">
                                    <label for="staticEmail" class="col-sm-4 col-5 col-form-label text-muted fw-bold"
                                        id="upi_title"></label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="upi_id">
                                                <input type="text" class="form-control text-indent-0" id="upi_id"
                                                    name="upi_id">
                                            </label>
                                        </div>
                                    </div>
                                    <label id="upi_id-error" class="error" for="upi_id"></label>
                                </div>
                                <button
                                    class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow">
                                    DEPOSIT
                                </button>
                            </form>

                        </div>
                        <!-- <div class="blues-box mt-3 text-center mb-4">
                        <iframe src='https://player.vimeo.com/video/740300187?h=7da6a3e555' height="300" width="440" frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                    </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('js')
    <script src="{{ url('user/deposit.js') }}?v={{ time() }}"></script>
    
    <!-- Fallback function definition -->
    <script>
        // console.log('Inline script loaded');
        
        // Fallback function definition
        if (typeof processMobileMoneyDeposit === 'undefined') {
            console.log('Function not found, defining fallback');
            function processMobileMoneyDeposit() {
              
                const amount = $("#mobile_money_amount").val();
                const phone = $("#mobile_money_phone").val();
             
                
                if (!amount || amount <= 0) {
                    alert("Please enter a valid amount");
                    return;
                }
                
                if (!phone || phone.length < 10) {
                    alert("Please enter a valid mobile number");
                    return;
                }
                
                alert('Form validation passed! Amount: ' + amount + ', Phone: ' + phone);
                
                // Show loading state
                const depositButton = $('button[onclick="processMobileMoneyDeposit()"]');
                const originalText = depositButton.text();
                depositButton.prop('disabled', true).text('Processing...');
                
                // Prepare form data for ZenoPay API
                const formData = {
                    amount: amount,
                    mobile_no: phone,
                    payment_gateway_type: '10',
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
        
                
                // Call the depositNow endpoint
                $.ajax({
                    url: '/depositNow',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        // console.log('Deposit API response:', response);
                        
                        if (response.success) {
                            alert('✅ ' + response.message);
                            
                            if (response.order_id) {
                                alert('Order ID: ' + response.order_id + '\n\nPlease complete the payment on your mobile device.');
                            }
                            
                            window.location.href = '/deposit?msg=' + encodeURIComponent(response.message) + '&order_id=' + (response.order_id || '');
                        } else {
                            alert('❌ Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.error('Deposit error:', xhr.responseText);
                        // alert('❌ Payment initialization failed. Please try again.');
                    },
                    complete: function() {
                        depositButton.prop('disabled', false).text(originalText);
                    }
                });
            }
            
            // Makeaccessible
            window.processMobileMoneyDeposit = processMobileMoneyDeposit;
            // console.log('Fallback function defined and attached to window');
        }
        
        // console.log('Final check - processMobileMoneyDeposit available:', typeof processMobileMoneyDeposit);
    </script>
    
    @isset($_GET['msg'])
    @if ($_GET['msg'] == 'Success')
        <script>
            toastr.success("Request send successfully!")
        </script>
    @else
        <script>
            toastr.success("{{ $_GET['msg'] }}")
        </script>
    @endif
    @endisset
@endsection
