@extends('Layout.usergame')
@section('content')
    <div class="deposite-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="pay-tabs">
                        <a href="#" class="custom-tabs-link active">DEPOSIT,</a>
                        <a href="/withdraw" class="custom-tabs-link">WITHDRAW</a>
                    </div>

                    <input type="hidden" name="username" id="username" value="">
                    <input type="hidden" name="password" id="password" value="">

                    <div class="pay-options">
                        <div class="payment-cols">
                            <div class="grid-view">
                                <div class="grid-list" onclick="paymentGatewayDetails('6')">
                                    <button class="btn payment-btn" data-tab="netbanking">
                                        <img src="images/app-logo/zeno.png" />
                                        <div class="PaymentCard_limit">Min {{setting('min_recharge')}}</div>
                                    </button>
                                </div>
                                <div class="grid-list" onclick="paymentGatewayDetails('3')">
                                    <button class="btn payment-btn" data-tab="upi">
                                        <img src="images/app-logo/masta.png" />
                                        <div class="PaymentCard_limit">Min {{setting('min_recharge')}}</div>
                                    </button>
                                </div>
                            </div>
                            <div class="deposite-box" id="netbanking">
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
                                                        Tshs
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
                                        <button class="btn amount-tooltips-btn">15000</button>
                                        <button class="btn amount-tooltips-btn active">30000</button>
                                        <button class="btn amount-tooltips-btn">75000</button>
                                        <button class="btn amount-tooltips-btn">150000</button>
                                    </div>
                                    <label for="net_bank_amount" class="error" id="net_bank_amount-error"></label>
                                </div>
                            </div>
                            <div class="deposite-box" id="upi">
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
                                                        Tshs
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
                                        <button class="btn amount-tooltips-btn">15000</button>
                                        <button class="btn amount-tooltips-btn active">30000</button>
                                        <button class="btn amount-tooltips-btn">15000</button>
                                        <button class="btn amount-tooltips-btn">150000</button>
                                    </div>
                                    <label for="upi_amount" class="error" id="upi_amount-error"></label>
                                    <div class="deposite-blc">
                                        <div>BALANCE AFTER DEPOSITING</div>
                                        <div class="dopsite-vlue">Tshs <span id="upi_amount_txt"></span></div>
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
                        <div class="white-box mt-3">
                            <h5 class="text-muted f-14 fw-bold">TO BE CREDITED</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="dopsite-vlue fw-bold f-20">
                                    <div>Tshs <span id="select_amount"></span></div>
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
                                <input type="hidden" name="amount" id="deposit_amount" value="9000">
                                <input type="hidden" name="payment_gateway_type" id="payment_gateway_type">
                                <input type="hidden" name="min_deposit_amount" id="min_deposit_amount">
                                <input type="hidden" name="max_deposit_amount" id="max_deposit_amount">

                                {{-- ✅ Mobile Number --}}
                                <div class="mb-3 row" id="mobile_div">
                                    <label for="mobile_no" class="col-sm-4 col-5 col-form-label text-muted fw-bold">
                                        Mobile Number
                                    </label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="mobile_no">
                                                <input type="text" 
                                                       class="form-control text-indent-0" 
                                                       id="mobile_no"
                                                       name="mobile_no"
                                                       maxlength="10"
                                                       pattern="07[0-9]{8}"
                                                       placeholder="07XXXXXXXX"
                                                       required>
                                            </label>
                                        </div>
                                    </div>
                                    <label id="mobile_no-error" class="error" for="mobile_no"></label>
                                </div>

                                {{-- ✅ Full Name --}}
                                <div class="mb-3 row" id="name_div">
                                    <label for="name" class="col-sm-4 col-5 col-form-label text-muted fw-bold">
                                        Full Name
                                    </label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="name">
                                                <input type="text" class="form-control text-indent-0" id="name"
                                                    name="name" required>
                                            </label>
                                        </div>
                                    </div>
                                    <label id="name-error" class="error" for="name"></label>
                                </div>

                                {{-- ✅ Email --}}
                                <div class="mb-3 row" id="email_div">
                                    <label for="email" class="col-sm-4 col-5 col-form-label text-muted fw-bold">
                                        Email
                                    </label>
                                    <div class="col-sm-8 col-7">
                                        <div class="login-controls">
                                            <label for="email">
                                                <input type="email" class="form-control text-indent-0" id="email"
                                                    name="email" required>
                                            </label>
                                        </div>
                                    </div>
                                    <label id="email-error" class="error" for="email"></label>
                                </div>

                                {{-- ✅ Network Dropdown --}}
                                <div class="row mb-3">
                                    <label for="networkSelect" class="col-sm-4 col-5 col-form-label text-muted fw-bold">
                                        Network Name
                                    </label>
                                    <div class="col-sm-8 col-7">
                                        <select class="form-select" id="networkSelect" name="network" required>
                                            <option selected disabled value="">Choose...</option>
                                            <option value="TIGO PESA">TIGO PESA</option>
                                            <option value="M PESA">M PESA</option>
                                            <option value="HALOPESA">HALOPESA</option>
                                            <option value="TTCL">TTCL</option>
                                            <option value="AIRTEL MONEY">Airtel Money</option>
                                        </select>
                                    </div>
                                </div>

                                <button
                                    class="register-btn rounded-pill d-flex align-items-center w-100 mt-3 orange-shadow">
                                    DEPOSIT
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('js')
    <script src="{{ url('user/deposit.js') }}"></script>
    @isset($_GET['msg'])
    @if ($_GET['msg'] == 'Success')
        <script>
            toastr.success("Please finish transaction with PIN on your phone!")
        </script>
    @endif
    @endisset
@endsection

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







undefined












undefined