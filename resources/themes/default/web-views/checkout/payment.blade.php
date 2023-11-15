@extends('layouts.front-end.app')

@section('title', translate('choose_Payment_Method'))

@push('css_or_js')
    <style>
        .stripe-button-el {
            display: none !important;
        }

        .razorpay-payment-button {
            display: none !important;
        }
    </style>

    {{--stripe--}}
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    {{--stripe--}}
@endpush

@section('content')
    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row">
            <div class="col-md-12 mb-5 pt-5">
                <div class="feature_header __feature_header">
                    <span>{{ translate('payment_method')}}</span>
                </div>
            </div>
            <section class="col-lg-8">
                <div class="checkout_details">
                @include('web-views.partials._checkout-steps',['step'=>3])
                <!-- Payment methods accordion-->
                    <h2 class="h6 pb-3 mb-2 mt-5">{{ translate('choose_payment')}}</h2>

                    <div class="row g-3">
                        @if(!$cod_not_show && $cash_on_delivery['status'])
                            <div class="col-sm-6" id="cod-for-cart">
                                <div class="card cursor-pointer">
                                    <div class="card-body __h-100px">
                                        <form action="{{route('checkout-complete')}}" method="get" class="needs-validation">
                                            <input type="hidden" name="payment_method" value="cash_on_delivery">
                                            <button class="btn btn-block click-if-alone" type="submit">
                                                <img width="150" class="__mt-n-10" src="{{asset('public/assets/front-end/img/cod.png')}}"/>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($digital_payment['status']==1)
                            @if(auth('customer')->check() && $wallet_status==1)
                                <div class="col-sm-6">
                                    <div class="card cursor-pointer">
                                        <div class="card-body __h-100px">
                                            <button class="btn btn-block click-if-alone" type="submit"
                                                data-toggle="modal" data-target="#wallet_submit_button">
                                                <img width="150" class="__mt-n-10"
                                                    src="{{asset('public/assets/front-end/img/wallet.png')}}"/>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @foreach ($payment_gateways_list as $payment_gateway)
                                <div class="col-sm-6">
                                    <div class="card cursor-pointer">
                                        <div class="card-body __h-100px overflow-hidden d-flex justify-content-center align-items-center">
                                            <form method="post" action="{{ route('customer.web-payment-request') }}">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ auth('customer')->check() ? auth('customer')->user()->id : session('guest_id') }}">
                                                <input type="hidden" name="customer_id" value="{{ auth('customer')->check() ? auth('customer')->user()->id : session('guest_id') }}">
                                                <input type="hidden" name="payment_method" value="{{ $payment_gateway->key_name }}">
                                                <input type="hidden" name="payment_platform" value="web">

                                                @if ($payment_gateway->mode == 'live' && isset($payment_gateway->live_values['callback_url']))
                                                    <input type="hidden" name="callback" value="{{ $payment_gateway->live_values['callback_url'] }}">
                                                @elseif ($payment_gateway->mode == 'test' && isset($payment_gateway->test_values['callback_url']))
                                                    <input type="hidden" name="callback" value="{{ $payment_gateway->test_values['callback_url'] }}">
                                                @else
                                                    <input type="hidden" name="callback" value="">
                                                @endif

                                                <input type="hidden" name="external_redirect_link" value="{{ url('/').'/web-payment' }}">
                                                @php($additional_data = $payment_gateway['additional_data'] != null ? json_decode($payment_gateway['additional_data']) : [])
                                                <button class="btn btn-block click-if-alone p-0 h-70" type="submit">
                                                    <img src="{{asset('storage/app/public/payment_modules/gateway_image')}}/{{$additional_data != null ? $additional_data->gateway_image : ''}}"
                                                         class="__inline-55 mt-0 h-100" alt="" onerror="this.src='{{asset('public/assets/front-end/img/img1.jpg')}}'">
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if(isset($offline_payment) && $offline_payment['status'])
                                <div class="col-sm-6" id="cod-for-cart">
                                    <div class="card cursor-pointer">
                                        <div class="card-body __h-100px overflow-hidden d-flex justify-content-center align-items-center">
                                            <form action="{{route('offline-payment-checkout-complete')}}" method="get" class="needs-validation">
                                                <span class="btn btn-block click-if-alone p-0"
                                                        data-toggle="modal" data-target="#pay_offline_modal">
                                                    <img width="150" class="__mt-n-10" src="{{asset('public/assets/front-end/img/pay-offline.png')}}"/>
                                                </span>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                    <!-- Navigation (desktop)-->
                    <div class="row justify-content-center">
                        <div class="col-md-6 text-center mt-5">
                            <a class="btn btn-secondary btn-block" href="{{route('checkout-details')}}">
                                <span class="d-none d-sm-inline">{{ translate('back_to_shipping')}}</span>
                                <span class="d-inline d-sm-none">{{ translate('back')}}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Sidebar-->
            @include('web-views.partials._order-summary')
        </div>
    </div>

    <!-- wallet modal -->
    @if(auth('customer')->check() && $wallet_status==1)
      <div class="modal fade" id="wallet_submit_button" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">{{ translate('wallet_payment')}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @php($customer_balance = auth('customer')->user()->wallet_balance)
            @php($remain_balance = $customer_balance - $amount)
            <form action="{{route('checkout-complete-wallet')}}" method="get" class="needs-validation">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="">{{ translate('your_current_balance')}}</label>
                            <input class="form-control" type="text" value="{{\App\CPU\Helpers::currency_converter($customer_balance)}}" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="">{{ translate('order_amount')}}</label>
                            <input class="form-control" type="text" value="{{\App\CPU\Helpers::currency_converter($amount)}}" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12">
                            <label for="">{{ translate('remaining_balance')}}</label>
                            <input class="form-control" type="text" value="{{\App\CPU\Helpers::currency_converter($remain_balance)}}" readonly>
                            @if ($remain_balance<0)
                            <label class="__color-crimson">{{ translate('you_do_not_have_sufficient_balance_for_pay_this_order!!')}}</label>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('close')}}</button>
                <button type="submit" class="btn btn--primary" {{$remain_balance>0? '':'disabled'}}>{{ translate('submit')}}</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    @endif

    <!-- offline payment modal -->
  <div class="modal fade" id="offline_payment_submit_button" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">{{translate('offline_payment')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('offline-payment-checkout-complete')}}" method="post" class="needs-validation">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="">{{translate('payment_by')}}</label>
                        <input class="form-control" type="text" name="payment_by" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="">{{translate('transaction_ID')}}</label>
                        <input class="form-control" type="text" name="transaction_ref" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12">
                        <label for="">{{translate('payment_note')}}</label>
                        <textarea name="payment_note" id="" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" value="offline_payment" name="payment_method">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{translate('close')}}</button>
            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
            </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="pay_offline_modal" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <form action="{{route('offline-payment-checkout-complete')}}" method="post" class="needs-validation">
                @csrf
                <div class="modal-body">

                    <div class="text-center px-5">
                        <img src="{{ asset('public/assets/front-end/img/offline-payments.png') }}" alt="">
                        <p class="py-2">
                            {{ translate('pay_your_bill_using_any_of_the_payment_method_below_and_input_the_required_information_in_the_form') }}
                        </p>
                    </div>

                    <div class="">

                        <select class="form-control" id="pay_offline_method" name="payment_by" required>
                            <option value="">{{ translate('select_Payment_Method') }}</option>
                            @foreach ($offline_payment_methods as $method)
                            <option value="{{ $method->id }}">{{ translate('payment_Method') }} :
                                {{ $method->method_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="" id="method-filed__div">
                        <div class="text-center py-5">
                            <img class="pt-5"
                                src="{{ asset('public/assets/front-end/img/offline-payments-vectors.png') }}" alt="">
                            <p class="py-2 pb-5 text-muted">{{ translate('select_a_payment_method first') }}</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        setTimeout(function () {
            $('.stripe-button-el').hide();
            $('.razorpay-payment-button').hide();
        }, 10)
    </script>

    <script type="text/javascript">
        function click_if_alone() {
            let total = $('.checkout_details .click-if-alone').length;
            if (Number.parseInt(total) < 2) {
                $('.click-if-alone').click()
                $('.checkout_details').html('<h1>{{translate("redirecting_to_the_payment")}}......</h1>');
            }
        }
        click_if_alone();

    </script>

    <script>
        $('#pay_offline_method').on('change', function () {
            pay_offline_method_field(this.value);
        });

        function pay_offline_method_field(method_id){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('pay-offline-method-list')}}" + "?method_id=" + method_id,
                data: {},
                processData: false,
                contentType: false,
                type: 'get',
                success: function (response) {
                    $("#method-filed__div").html(response.methodHtml);
                },
                error: function () {

                }
            });
        }
    </script>
@endpush
