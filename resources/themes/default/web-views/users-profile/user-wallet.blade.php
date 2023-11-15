@extends('layouts.front-end.app')

@section('title',translate('my_Wallet'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/front-end')}}/css/owl.carousel.min.css"/>
    <link rel="stylesheet" href="{{asset('public/assets/front-end')}}/css/owl.theme.default.min.css"/>
@endpush

@section('content')

    <div class="container text-center">
        <h3 class="headerTitle my-3">{{translate('wallet')}}</h3>
    </div>

    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row g-3">
            <!-- Sidebar-->
        @include('web-views.partials._profile-aside')
        <!-- Content  -->

            <div class="col-lg-9 col-md-9">
                <div class="card">
                    <div class="card-body p-2">
                        <div class="row g-0 g-md-3 h-100">

                            @php($add_funds_to_wallet = \App\CPU\Helpers::get_business_settings('add_funds_to_wallet'))

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="card btn--primary h-100 position-relative mx-h-200">
                                            <div class="card-body d-flex align-items-center z-2">
                                                <div class="d-flex flex-wrap justify-content-between align-items-center w-100">
                                                    <div class="text-white">
                                                        <p class="mb-2">{{translate('wallet')}}</p>
                                                        <p class="mb-0">{{translate('amount')}}</p>
                                                    </div>
                                                    @if ($add_funds_to_wallet)
                                                    <div class="mx-2">
                                                        <button class="btn btn-light align-items-center" data-toggle="modal" data-target="#addFundToWallet">
                                                            <i class="tio-add-circle text-accent"></i>
                                                            <strong class="text-accent">{{ translate('add_Fund') }}</strong>
                                                        </button>
                                                    </div>
                                                    @endif

                                                    <h2 class="fs-36 text-white d-flex align-items-center m-0">
                                                        {{\App\CPU\Helpers::currency_converter($total_wallet_balance)}}

                                                        @if ($add_funds_to_wallet)
                                                        <span class="ml-2 fs-18">
                                                            <i class="tio-info-outined" data-toggle="tooltip" data-placement="bottom" title="{{ translate('if_you_want_to_add_fund_to_your_wallet_then_click_add_fund_button') }}"></i>
                                                        </span>
                                                        @endif

                                                    </h2>
                                                </div>
                                            </div>
                                            <img class="wallet-card-bg z-1" src="{{ asset('public/assets/front-end/img/icons/wallet-card.png') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        @if($add_funds_to_wallet)
                                        <div class="owl-carousel add-fund-carousel">
                                            @foreach ($add_fund_bonus_list as $bonus)
                                                    <div class="item">

                                                <div class="add-fund-carousel-card z-1 w-100 border rounded-10 p-4 ml-1">
                                                        <div>
                                                            <h4 class="mb-2 text-accent">{{ $bonus->title }}</h4>
                                                            <p class="mb-2 text-dark">{{ translate('valid_till') }} {{ date('d M, Y',strtotime($bonus->end_date_time)) }}</p>
                                                        </div>
                                                        <div>
                                                            @if ($bonus->bonus_type == 'percentage')
                                                            <p>{{ translate('add_fund_to_wallet') }} {{ \App\CPU\Helpers::currency_converter($bonus->min_add_money_amount) }} {{ translate('and_enjoy') }} {{ $bonus->bonus_amount }}% {{ translate('bonus') }}</p>
                                                            @else
                                                                <p>{{ translate('add_fund_to_wallet') }} {{ \App\CPU\Helpers::currency_converter($bonus->min_add_money_amount) }} {{ translate('and_enjoy') }} {{ \App\CPU\Helpers::currency_converter($bonus->bonus_amount) }} {{ translate('bonus') }}</p>
                                                            @endif
                                                            <p class="fw-bold text-accent mb-0">{{ $bonus->description ? Str::limit($bonus->description, 50):'' }}</p>
                                                        </div>
                                                        <img class="slider-card-bg-img" width="50" src="{{ asset('public/assets/front-end/img/icons/add_fund_vector.png') }}" alt="">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="addFundToWallet" tabindex="-1" aria-labelledby="addFundToWalletModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body px-5">

                                            <form action="{{ route('customer.add-fund-request') }}" method="post">
                                                @csrf
                                                <div class="pb-4">
                                                    <h4 class="text-center">{{ translate('add_Fund_to_Wallet') }}</h4>
                                                    <p class="text-center">{{ translate('add_fund_by_from_secured_digital_payment_gateways') }}</p>
                                                    <input type="number" class="h-70 form-control text-center text-24 rounded-10" id="add-fund-amount-input" name="amount" required placeholder="{{ \App\CPU\currency_symbol() }}500">
                                                    <input type="hidden" value="web" name="payment_platform" required>
                                                    <input type="hidden" value="{{ request()->url() }}" name="external_redirect_link" required>
                                                </div>

                                                <div id="add-fund-list-area" style="display: none">
                                                    <h6 class="mb-2">{{ translate('payment_Methods') }} <small>({{ translate('faster_&_secure_way_to_pay_bill') }})</small></h6>
                                                    <div class="gatways_list">

                                                        @forelse ($payment_gateways as $gateway)
                                                            <label class="form-check form--check rounded">
                                                                <input type="radio" class="form-check-input d-none" name="payment_method" value="{{ $gateway->key_name }}" required>
                                                                <div class="check-icon">
                                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <circle cx="8" cy="8" r="8" fill="#1455AC"/>
                                                                    <path d="M9.18475 6.49574C10.0715 5.45157 11.4612 4.98049 12.8001 5.27019L7.05943 11.1996L3.7334 7.91114C4.68634 7.27184 5.98266 7.59088 6.53004 8.59942L6.86856 9.22314L9.18475 6.49574Z" fill="white"/>
                                                                    </svg>
                                                                </div>
                                                                @php( $payment_method_title = !empty($gateway->additional_data) ? (json_decode($gateway->additional_data)->gateway_title ?? ucwords(str_replace('_',' ', $gateway->key_name))) : ucwords(str_replace('_',' ', $gateway->key_name)) )
                                                                @php( $payment_method_img = !empty($gateway->additional_data) ? json_decode($gateway->additional_data)->gateway_image : '' )
                                                                <div class="form-check-label d-flex align-items-center">
                                                                    <img width="60" src="{{ asset('storage/app/public/payment_modules/gateway_image/'.$payment_method_img) }}"
                                                                    onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'"
                                                                    alt="img" >
                                                                    <span class="ml-3">{{ $payment_method_title }}</span>
                                                                </div>
                                                            </label>
                                                        @empty

                                                        @endforelse
                                                    </div>
                                                </div>


                                                <div class="d-flex justify-content-center pt-2 pb-3">
                                                    <button type="submit" class="btn btn--primary w-75 mx-3" id="add_fund_to_wallet_form_btn">{{ translate('add_Fund') }}</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="">
                                    <div class="align-items-start d-flex flex-column flex-md-row gap-8 justify-content-between p-2">
                                        <h6 class="mb-0">{{ translate('Transaction_History') }}</h6>

                                        <ul class="navbar-nav text-center" style="{{Session::get('direction') === "rtl" ? 'padding-right: 0px' : ''}}">
                                            <div class="dropdown border pl-3">
                                                <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                        style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 0">
                                                    {{ request()->has('type') ? (request('type') == 'all'? translate('all_Transactions') : ucwords(translate(request('type')))):translate('all_Transactions')}}
                                                </button>

                                                <div class="dropdown-menu __dropdown-menu-3 __min-w-165px" aria-labelledby="dropdownMenuButton"
                                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};transform: translate3d(7px, -230px, 0) !important;">

                                                    <a class="dropdown-item" href="{{route('wallet')}}/?type=all">
                                                        {{translate('all_Transaction')}}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="{{route('wallet')}}/?type=order_transactions">
                                                        {{translate('order_transactions')}}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="{{route('wallet')}}/?type=order_refund">
                                                        {{translate('order_refund')}}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="{{route('wallet')}}/?type=converted_from_loyalty_point">
                                                        {{translate('converted_from_loyalty_point')}}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="{{route('wallet')}}/?type=added_via_payment_method">
                                                        {{translate('added_via_payment_method')}}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="{{route('wallet')}}/?type=add_fund_by_admin">
                                                        {{translate('add_fund_by_admin')}}
                                                    </a>
                                                </div>

                                            </div>
                                        </ul>
                                    </div>

                                    <div class="max-height-500">
                                        <div class="d-flex flex-column gap-2">
                                            @foreach($wallet_transactio_list as $key=>$item)
                                            <div class="bg-light my-1 p-3 p-sm-3 rounded d-flex justify-content-between g-2">
                                                <div class="w-100">
                                                    <h6 class="mb-2 d-flex align-items-center gap-8">
                                                        @if($item['debit'] != 0)
                                                            <img src="{{ asset('public/assets/front-end/img/icons/coin-danger.png') }}" width="25" alt="">
                                                        @else
                                                            <img src="{{ asset('public/assets/front-end/img/icons/coin-success.png') }}" width="25" alt="">
                                                        @endif

                                                        {{ $item['debit'] != 0 ? ' - '.\App\CPU\Helpers::currency_converter($item['debit']) : ' + '.\App\CPU\Helpers::currency_converter($item['credit']) }}

                                                    </h6>
                                                    <h6 class="text-muted mb-0 small">
                                                        @if ($item['transaction_type'] == 'add_fund_by_admin')
                                                            {{translate('add_fund_by_admin')}} {{ $item['reference'] =='earned_by_referral' ? '('.translate($item['reference']).')' : '' }}
                                                        @elseif($item['transaction_type'] == 'order_place')
                                                            {{translate('order_place')}}
                                                        @elseif($item['transaction_type'] == 'loyalty_point')
                                                            {{translate('converted_from_loyalty_point')}}
                                                        @elseif($item['transaction_type'] == 'add_fund')
                                                            {{translate('added_via_payment_method')}}
                                                        @else
                                                            {{ ucwords(translate($item['transaction_type'])) }}
                                                        @endif
                                                    </h6>
                                                </div>
                                                <div class="text-end small">
                                                    <div class="text-muted mb-1 text-nowrap">{{date('d M, Y H:i A',strtotime($item['created_at']))}}</div>
                                                        @if($item['debit'] != 0)
                                                            <p class="text-danger fs-12">{{translate('debit')}}</p>
                                                        @else
                                                            <p class="text-info fs-12 m-0">{{translate('credit')}}</p>
                                                        @endif
                                                </div>
                                            </div>

                                            @if ($item['admin_bonus'] > 0)
                                            <div class="bg-light my-1 p-3 p-sm-3 rounded d-flex justify-content-between g-2">
                                                <div class="">
                                                    <h6 class="mb-2 d-flex align-items-center gap-8">
                                                        <img src="{{ asset('public/assets/front-end/img/icons/coin-success.png') }}" width="25" alt="">
                                                        <span>+ {{ \App\CPU\Helpers::currency_converter($item['admin_bonus']) }}</span>
                                                    </h6>
                                                    <h6 class="text-muted mb-0 small">
                                                        {{ucwords(str_replace('_', ' ', translate('admin_bonus')))}}
                                                    </h6>
                                                </div>
                                                <div class="text-end small">
                                                    <div class="text-muted mb-1 text-nowrap">{{date('d M, Y H:i A',strtotime($item['created_at']))}}</div>
                                                        @if($item['debit'] != 0)
                                                            <p class="text-danger fs-12">{{translate('debit')}}</p>
                                                        @else
                                                            <p class="text-info fs-12 m-0">{{translate('credit')}}</p>
                                                        @endif
                                                </div>
                                            </div>
                                            @endif

                                            @endforeach
                                        </div>
                                    </div>
                                    @if($wallet_transactio_list->count()==0)
                                    <div class="d-flex flex-column gap-3 align-items-center text-center my-5">
                                        <img width="72" src="{{ asset('public/assets/front-end/img/icons/empty-transaction-history.png')}}" class="dark-support" alt="">
                                        <h6 class="text-muted mt-3">{{translate('you_do_not_have_any')}}<br> {{ request('type') != 'all' ? ucwords(translate(request('type'))) : '' }} {{translate('transaction_yet')}}</h6>
                                    </div>
                                    @endif

                                    <div class="card-footer bg-transparent border-0 p-0 mt-3">

                                        @if (request()->has('type'))
                                            @php($paginationLinks = $wallet_transactio_list->links())
                                            @php($modifiedLinks = preg_replace('/href="([^"]*)"/', 'href="$1&type='.request('type').'"', $paginationLinks))
                                        @else
                                            @php($modifiedLinks = $wallet_transactio_list->links())
                                        @endif

                                        {!! $modifiedLinks !!}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
{{-- Owl Carousel --}}
<script src="{{asset('public/assets/front-end')}}/js/owl.carousel.min.js"></script>
<script>

    $(document).ready(function(){
        const img = $("img");
        img.on("error", function (event) {
            event.target.src = '{{asset('public/assets/front-end/img/image-place-holder.png')}}';
            event.onerror = null
        })
    });

    $('.add-fund-carousel').owlCarousel({
        loop: true,
        dots: true,
        autoplay: false,
        nav: false,
        margin: 20,
        autoWidth:true,
        items: 1
    })

    $('#add_fund_to_wallet_form_btn').on('click', function(){
        if (!$("input[name='payment_method']:checked").val()) {
            toastr.error("{{ translate('please_select_a_payment_Methods') }}");
        }
    })

    $('#add-fund-amount-input').on('keyup', function(){
        if($(this).val() == ''){
            $('#add-fund-list-area').slideUp();
        }else{
            if (!isNaN($(this).val()) && $(this).val() < 0) {
                $(this).val(0);
                toastr.error("{{ translate('cannot_input_minus_value') }}");
            } else {
                $('#add-fund-list-area').slideDown();
            }
        }
    })
</script>
@endpush
