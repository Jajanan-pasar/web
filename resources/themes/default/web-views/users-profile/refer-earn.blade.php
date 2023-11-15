@extends('layouts.front-end.app')

@section('title',auth('customer')->user()->f_name.' '.auth('customer')->user()->l_name)


@section('content')
    <!-- Page Title-->
    <div class="container rtl">
        <h3 class="py-3 m-0 text-center headerTitle">{{translate('refer_&_Earn')}}</h3>
    </div>
    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 rtl">
        <div class="row g-3">
        <!-- Sidebar-->
        @include('web-views.partials._profile-aside')
        <!-- Content  -->
            <section class="col-lg-9 col-md-9 __customer-profile">
                <div class="card box-shadow-sm">
                    <div class="card-header refer_and_earn_section">

                        <div class="d-flex justify-content-center align-items-center py-2 mb-3">
                            <div class="banner-img">
                                <img class="img-fluid" src="{{ asset('public/assets/front-end/img/refer-and-earn.png') }}" alt="" width="300">
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="primary-heading mb-2">{{ translate('invite_Your_Friends_&_Businesses') }}</h5>
                            <p class="secondary-heading">{{ translate('copy_your_code_and_share_your_friends') }}</p>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <h6 class="text-secondary-color">{{ translate('your_personal_code') }}</h6>
                                <div class="refer_code_box">
                                    <div class="refer_code" onclick="click_to_copy('{{ $customer_detail->referral_code }}')">{{ $customer_detail->referral_code }}</div>
                                    <span class="refer_code_copy" onclick="click_to_copy('{{ $customer_detail->referral_code }}')">
                                        <img class="w-100" src="{{ asset('public/assets/front-end/img/icons/solar_copy-bold-duotone.png') }}" alt="">
                                    </span>
                                </div>

                                <h4 class="share-icons-heading">{{ translate('oR_SHARE') }}</h4>
                                <div class="d-flex justify-content-center align-items-center share-on-social">

                                    @php
                                        $text = "Greetings,6Valley is the best e-commerce platform in the country.If you are new to this website dont forget to use " . $customer_detail->referral_code . " " ."as the referral code while sign up into 6valley.";
                                        $link = url('/');
                                    @endphp
                                    <a href="https://api.whatsapp.com/send?text={{$text}}.{{$link}}" target="_blank">
                                        <img src="{{ asset('public/assets/front-end/img/icons/whatsapp.png') }}" alt=""
                                        onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'">
                                    </a>
                                    <a href="mailto:recipient@example.com?subject=Referral%20Code%20Text&body={{$text}}%20Link:%20{{$link}}"target="_blank">
                                        <img src="{{ asset('public/assets/front-end/img/icons/gmail.png') }}" alt=""
                                        onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'">
                                    </a>
                                    <a href="javascript:" onclick="click_to_copy('{{ route('home') }}?referral_code={{ $customer_detail->referral_code }}')">
                                        <img src="{{ asset('public/assets/front-end/img/icons/share.png') }}" alt=""
                                        onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'">
                                    </a>
                                </div>
                            </div>

                            <div class="information-section col-md-10">
                                <h4 class="text-bold d-flex align-items-center"> <span class="custom-info-icon">i</span> {{ translate('how_you_it_works') }}?</h4>

                                <ul>
                                    <li>
                                        <span class="item-custom-index">{{ translate('1') }}</span>
                                        <span class="item-custom-text">{{ translate('invite_your_friends_&_businesses') }}</span>
                                    </li>
                                    <li>
                                        <span class="item-custom-index">{{ translate('2') }}</span>
                                        <span class="item-custom-text">{{ translate('they_register') }} {{ $web_config['name']->value }} {{ translate('with_special_offer') }}</span>
                                    </li>
                                    <li>
                                        <span class="item-custom-index">{{ translate('3') }}</span>
                                        <span class="item-custom-text">{{ translate('you_made_your_earning') }}</span>
                                    </li>
                                </ul>
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
function click_to_copy(copied_text) {
    navigator.clipboard.writeText(copied_text)
        .then(function () {
            toastr.success("{{ translate('successfully_copied') }}");
        })
        .catch(function (error) {
            toastr.error("{{ translate('copied_failed') }}");
        });
}
</script>
@endpush
