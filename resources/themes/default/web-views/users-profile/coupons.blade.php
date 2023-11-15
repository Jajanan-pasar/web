@extends('layouts.front-end.app')

@section('title',auth('customer')->user()->f_name.' '.auth('customer')->user()->l_name)


@section('content')
    <!-- Page Title-->
    <div class="container rtl">
        <h3 class="py-3 m-0 text-center headerTitle">{{translate('coupons')}}</h3>
    </div>
    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 rtl">
        <div class="row g-3">
        <!-- Sidebar-->
        @include('web-views.partials._profile-aside')
        <!-- Content  -->
            <section class="col-lg-9 col-md-9 __customer-profile">
                <div class="card box-shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ($coupons as $item)
                            <div class="col-lg-6">
                                <div class="ticket-box">
                                    <div class="ticket-start">

                                        @if ($item->coupon_type == "free_delivery")
                                            <img width="30" src="{{ asset('public/assets/front-end/img/icons/bike.png') }}" alt="">
                                        @elseif ($item->coupon_type != "free_delivery" && $item->discount_type == "percentage")
                                            <img width="30" src="{{ asset('public/assets/front-end/img/icons/fire.png') }}" alt="">
                                        @elseif ($item->coupon_type != "free_delivery" && $item->discount_type == "amount")
                                            <img width="30" src="{{ asset('public/assets/front-end/img/icons/dollar.png') }}" alt="">
                                        @endif

                                        <h2 class="ticket-amount">
                                        @if ($item->coupon_type == "free_delivery")
                                            {{ translate('free_Delivery') }}
                                        @else
                                            {{ ($item->discount_type == 'percentage')? $item->discount.'% Off':\App\CPU\Helpers::currency_converter($item->discount)}}
                                        @endif
                                        </h2>
                                        <p>
                                            {{ translate('on') }}

                                            @if($item->seller_id == '0')
                                                {{ translate('All_Shops') }}
                                            @elseif($item->seller_id == NULL)
                                                <a class="shop-name" href="{{route('shopView',['id'=>0])}}">
                                                    {{ $web_config['name']->value }}
                                                </a>
                                            @else
                                                <a class="shop-name" href="{{isset($item->seller->shop) ? route('shopView',['id'=>$item->seller->shop['id']]) : 'javascript:'}} ">
                                                    {{ isset($item->seller->shop) ? $item->seller->shop->name : translate('shop_not_found') }}
                                                </a>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="ticket-border"></div>
                                    <div class="ticket-end">
                                        <button class="ticket-welcome-btn couponid couponid-{{ $item->code }}" onclick="click_to_copy_coupon('{{ $item->code }}')">{{ $item->code }}</button>
                                        <button class="ticket-welcome-btn couponid-hide couponhideid-{{ $item->code }} d-none">{{ translate('copied') }}</button>
                                        <h6>{{ translate('valid_till') }} {{ $item->expire_date->format('d M, Y') }}</h6>
                                        <p class="m-0">{{ translate('available_from_minimum_purchase') }} {{\App\CPU\Helpers::currency_converter($item->min_purchase)}}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <div class="col-md-12 mt-2">
                                {{ $coupons->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('script')
<script>
function click_to_copy_coupon(copied_text) {
    navigator.clipboard.writeText(copied_text)
        .then(function () {
            toastr.success("{{ translate('successfully_copied') }}");
            $('.couponid-hide').addClass("d-none");
            $('.couponid').removeClass("d-none");
            $('.couponid-'+copied_text).addClass("d-none");
            $('.couponhideid-'+copied_text).removeClass("d-none");
        })
        .catch(function (error) {
            toastr.error("{{ translate('copied_failed') }}");
        });
}
</script>
@endpush
