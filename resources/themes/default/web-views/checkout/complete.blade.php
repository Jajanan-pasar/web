@extends('layouts.front-end.app')

@section('title', translate('order_Complete'))

@push('css_or_js')
    <style>

        .spanTr {
            color: {{$web_config['primary_color']}};
        }

        .amount {
            color: {{$web_config['primary_color']}};
        }

        @media (max-width: 600px) {
            .orderId {
                margin- {{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 91px;
            }
        }
        /*  */
    </style>
@endpush

@section('content')
    <div class="container mt-5 mb-5 rtl __inline-53"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row d-flex justify-content-center">
            <div class="col-md-10 col-lg-10">
                <div class="card">
                    @if(auth('customer')->check() || session('guest_id'))
                        <div class="p-5">

                            <div class="row mb-2">
                                <div class="col-12">
                                    <center>
                                        <i class="fa fa-check-circle __text-50px __color-0f9d58"></i>
                                    </center>
                                </div>
                            </div>

                            <h4 class="font-black text-center">{{ translate('order_Placed_Successfully')}}!</h4>

                            @if (isset($order_ids) && count($order_ids) > 0)
                                <h6 class="text-center">{{ translate('your_Order_ID') }} -
                                @foreach ($order_ids as $key => $order)

                                    {{ $order }},

                                @endforeach

                                    {{ translate('thank_you_for_your_order') }}! {{ translate('your_order_has_been_processed.') }} {{ translate('check_your_email_to_get_the_order_id_and_details.') }}</h6>
                            @else
                                <h6 class="text-center">{{ translate('thank_you_for_your_order') }}! {{ translate('your_order_has_been_processed.') }} {{ translate('check_your_email_to_get_the_order_id_and_details.') }}</h6>
                            @endif

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <a href="{{ route('track-order.index') }}" class="btn btn--primary mb-3 text-center">
                                        {{ translate('track_Order')}}
                                    </a>

                                </div>
                                <div class="col-12 text-center">
                                    <a href="{{route('home')}}" class="text-center">{{ translate('Continue_Shopping') }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
