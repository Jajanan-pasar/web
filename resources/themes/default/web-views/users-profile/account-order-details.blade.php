@extends('layouts.front-end.app')

@section('title',translate('order_Details'))

@push('css_or_js')
    <style>
        .page-item.active .page-link {
            background-color: {{$web_config['primary_color']}}              !important;
        }

        .amount {
            margin- {{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 60px;

        }

        .w-49{
            width: 49% !important
        }

        a {
            color: {{$web_config['primary_color']}};
        }

        @media (max-width: 360px) {
            .for-glaxy-mobile {
                margin- {{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 6px;
            }

        }

        @media (max-width: 600px) {

            .for-glaxy-mobile {
                margin- {{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 6px;
            }

            .order_table_info_div_2 {
                text-align: {{Session::get('direction') === "rtl" ? 'left' : 'right'}}          !important;
            }

            .spandHeadO {
                margin- {{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 16px;
            }

            .spanTr {
                margin- {{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 16px;
            }

            .amount {
                margin- {{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 0px;
            }

        }
    </style>
@endpush

@section('content')

    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row g-3">
            <!-- Sidebar-->
            @include('web-views.partials._profile-aside')

            {{-- Content --}}
            <section class="col-lg-9 col-md-9">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <a class="page-link" href="{{ route('account-oder') }}">
                            <i class="czi-arrow-{{Session::get('direction') === "rtl" ? 'right ml-2' : 'left mr-2'}}"></i>{{translate('back')}}
                        </a>
                    </div>
                </div>
                <div class="card box-shadow-sm">

                    @if(isset($order['seller_id']) != 0)
                        @php($shopName=\App\Model\Shop::where('seller_id', $order['seller_id'])->first())
                    @endif
                    <div class="text-white">
                        <div class="row m-0 g-3" style="background: {{$web_config['primary_color']}}">

                            @if($order->order_type == 'default_type')
                            <div class="col-md-12 col-lg-12">
                                <div class="d-flex justify-content-between gap-2 flex-wrap">
                                    <div class="d-flex gap-3 flex-wrap">

                                    </div>
                                    <div class="d-flex align-items-start gap-2">
                                        @if($order->order_status=='delivered')
                                            <div class="text-end">
                                                <button class="btn btn-light align-items-center mt-2" onclick="order_again({{ $order->id }})">
                                                    {{ translate('reorder') }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6 col-lg-4">
                                <div class="p-2">
                                    <h6 class="text-white">{{translate('order_Info')}}</h6>
                                    <div class="small">
                                        <p class="m-0">{{translate('order_ID')}} : <span class="font-bold">{{ $order->id }}</span></p>

                                        @if($order->order_type == 'default_type' && \App\CPU\Helpers::get_business_settings('order_verification'))
                                            <p class="m-0">{{translate('Verification_Code')}} : <span class="font-bold">{{ $order['verification_code'] }}</span></p>
                                        @endif

                                        <p class="m-0">{{translate('order_date')}} : <span class="font-bold">{{ date('d M, Y',strtotime($order->created_at)) }}</span></p>
                                    </div>
                                </div>
                            </div>


                            @if( $order->order_type == 'default_type')
                            <div class="col-md-12 col-lg-4">
                                <div class="p-2">
                                    <h6 class="text-white">{{translate('shipping_address')}}</h6>

                                    @if($order->shippingAddress)
                                        @php($shipping=$order->shippingAddress)
                                    @else
                                        @php($shipping=json_decode($order['shipping_address_data']))
                                    @endif

                                    <p class="font-bold small mb-0">
                                        @if($shipping)
                                        {{$shipping->address}},<br>
                                        {{$shipping->city}}
                                        , {{$shipping->zip}}

                                        @endif
                                    </p>

                                </div>
                            </div>

                            <div class="col-md-12 col-lg-4">
                                <div class="p-2">
                                    <h6 class="text-white">{{translate('billing_address')}}</h6>

                                    @if($order->billingAddress)
                                        @php($billing=$order->billingAddress)
                                    @else
                                        @php($billing=json_decode($order['billing_address_data']))
                                    @endif

                                    <p class="font-bold small mb-0">
                                    @if($billing)
                                        {{$billing->address}}, <br>
                                        {{$billing->city}}
                                        , {{$billing->zip}}
                                    @else
                                        {{$shipping->address}},<br>
                                        {{$shipping->city}}
                                        , {{$shipping->zip}}
                                    @endif
                                    </p>

                                </div>
                            </div>
                            @endif

                            <!-- offline_payment -->
                            @if($order->payment_method == 'offline_payment' && isset($order->offline_payments))
                            <div class="col-md-12 col-lg-12">
                                <div class="p-2">
                                    <h6 class="text-white">{{translate('offline_payments_info')}}</h6>
                                    @foreach (json_decode($order->offline_payments->payment_info) as $key=>$item)
                                        @if ($key != 'method_id' && $key != 'method_name')
                                            <div class="small">
                                                <p class="m-0">{{translate($key)}} : <span class="font-bold">{{ $item }}</span></p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="payment mb-3 table-responsive">
                        <table class="table table-borderless" style="min-width:600px">
                            <thead class="thead-light text-capitalize">
                                <tr>
                                    <th>{{translate('order_details')}}</th>
                                    <th>{{translate('qty')}}</th>
                                    <th>{{translate('price')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->details as $key=>$detail)
                                    @php($product=json_decode($detail->product_details,true))
                                    @if($product)
                                        <tr>
                                            <td class="for-tab-img" >
                                                <div class="media gap-3">

                                                    @if($detail->product_all_status)
                                                        <img class="d-block" onclick="location.href='{{route('product',$product['slug'])}}'"
                                                        onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                        src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$detail->product_all_status['thumbnail']}}"
                                                        alt="VR Collection" width="100">
                                                    @else
                                                        <img class="d-block" onclick="location.href='{{route('product',$product['slug'])}}'"
                                                        onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                        src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$product['thumbnail']}}"
                                                        alt="VR Collection" width="100">
                                                    @endif

                                                    <div class="media-body">
                                                        <a href="{{route('product',[$product['slug']])}}">
                                                            {{isset($product['name']) ? Str::limit($product['name'],40) : ''}}
                                                        </a>
                                                        @if($detail->refund_request == 1)
                                                            <small> ({{translate('refund_pending')}}) </small> <br>
                                                        @elseif($detail->refund_request == 2)
                                                            <small> ({{translate('refund_approved')}}) </small> <br>
                                                        @elseif($detail->refund_request == 3)
                                                            <small> ({{translate('refund_rejected')}}) </small> <br>
                                                        @elseif($detail->refund_request == 4)
                                                            <small> ({{translate('refund_refunded')}}) </small> <br>
                                                        @endif<br>
                                                        @if($detail->variant)
                                                            <small>
                                                                <span>{{translate('variant')}} : </span>
                                                                {{$detail->variant}}
                                                            </small>
                                                        @endif

                                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                                            @if($detail->product && $order->payment_status == 'paid' && $detail->product->digital_product_type == 'ready_product')
                                                                <a class="btn btn-sm rounded btn--primary" onclick="digital_product_download('{{ route('digital-product-download', $detail->id) }}')" href="javascript:">{{translate('download')}} <i class="tio-download-from-cloud"></i></a>
                                                            @elseif($detail->product && $order->payment_status == 'paid' && $detail->product->digital_product_type == 'ready_after_sell')
                                                                @if($detail->digital_file_after_sell)
                                                                    <a class="btn btn-sm rounded btn--primary" onclick="digital_product_download('{{ route('digital-product-download', $detail->id) }}')" href="javascript:">       {{translate('download')}} <i class="tio-download-from-cloud"></i>
                                                                    </a>
                                                                @else

                                                                    <span class="" data-toggle="tooltip" data-placement="top" title="{{translate('product_not_uploaded_yet')}}">
                                                                        <a class="btn btn-sm rounded btn--primary disabled">{{translate('download')}} <i class="tio-download-from-cloud"></i></a>
                                                                    </span>
                                                                @endif
                                                            @endif
                                                            <?php
                                                                $refund_day_limit = \App\CPU\Helpers::get_business_settings('refund_day_limit');
                                                                $order_details_date = $detail->created_at;
                                                                $current = \Carbon\Carbon::now();
                                                                $length = $order_details_date->diffInDays($current);
                                                            ?>

                                                            @if($order->order_type == 'default_type')
                                                                @if($order->order_status=='delivered')
                                                                    <button type="button" class="btn btn-sm rounded btn-warning" data-toggle="modal" onclick="location.href='{{route('submit-review',[$detail->id])}}'">
                                                                        <i class="tio-star-half"></i>{{translate('review')}}
                                                                    </button>

                                                                    @if($detail->refund_request !=0)
                                                                    <button type="button" class="btn btn-sm rounded btn--primary" data-toggle="modal" onclick="location.href='{{route('refund-details',[$detail->id])}}'">
                                                                        {{translate('refund_details')}}
                                                                    </button>
                                                                    @endif

                                                                    @if( $length <= $refund_day_limit && $detail->refund_request == 0)
                                                                        <a href="{{route('refund-request',[$detail->id])}}"
                                                                            class="btn btn--primary btn-sm d-inline-block">{{translate('refund_request')}}</a>
                                                                    @endif
                                                                @endif
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="word-nobreak">{{$detail->qty}}</span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold amount">{{\App\CPU\Helpers::currency_converter($detail->price)}} </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    <!--Calculation-->
                    @php($summary=\App\CPU\OrderManager::order_summary($order))

                    @php($extra_discount=0)

                    <?php
                        if ($order['extra_discount_type'] == 'percent') {
                            $extra_discount = ($summary['subtotal'] / 100) * $order['extra_discount'];
                        } else {
                            $extra_discount = $order['extra_discount'];
                        }
                    ?>

                    @if($order->delivery_type !=null)
                    <div class="payment mb-3 table-responsive">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                                <div class="px-3">
                                    <h5 class="text-black mt-0 m-0 text-capitalize">{{translate('deliveryman_info')}} </h5>
                                </div>

                                <div class="d-flex">
                                    <div class="col-sm-auto">
                                        @if ($order->delivery_type == 'self_delivery' && $order->delivery_man_id  && isset($order->delivery_man))
                                            @if($order->order_type == 'default_type')
                                                <button class="btn btn-outline--info btn-sm" data-toggle="modal" data-target="#exampleModal">
                                                    <i class="fa fa-envelope"></i>
                                                    {{translate('chat_with_deliveryman')}}
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="col-sm-auto">
                                        @if($order->order_type == 'default_type' && $order->order_status=='delivered' && $order->delivery_man_id)
                                            <a href="{{route('deliveryman-review',[$order->id])}}"
                                            class="btn btn-outline--info btn-sm">
                                                <i class="czi-star mr-1 font-size-md"></i>
                                                {{ $order->delivery_man_review ? translate('update_review') : '' }}
                                                {{translate('give_review')}}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row m-2 justify-content-between">
                                <div class="col-sm-12">
                                    @if ($order->delivery_type == 'self_delivery' && $order->delivery_man_id  && isset($order->delivery_man))
                                        <p class="__text-414141">
                                        <span class="text-capitalize">
                                            {{translate('name')}} : {{$order->delivery_man['f_name'].' '.$order->delivery_man['l_name']}}
                                        </span>
                                        </p>

                                        @if (count($order->verification_images)>0 && $order->verification_status == 1)
                                            <div class="w-100 mt-4">
                                                <div class="card border-0">
                                                    <div class="card-body p-0">
                                                        <h6 class="text-muted mb-3">
                                                            <span class="text-base mr-2">
                                                                <img src="{{ asset('public/assets/front-end/img/icons/camera-icon.svg') }}" alt="" width="15">
                                                            </span>
                                                            {{ translate('picture_Upload_by') }} {{$order->delivery_man->f_name}}&nbsp{{$order->delivery_man->l_name}}
                                                        </h6>

                                                        <div class="d-flex flex-wrap gap-3">
                                                            @foreach ($order->verification_images as $image)
                                                                @if(file_exists(base_path("storage/app/public/delivery-man/verification-image/".$image->image)))
                                                                <img src="{{asset("storage/app/public/delivery-man/verification-image/".$image->image)}}" class="max-height-100 rounded remove-mask-img"
                                                                onerror="this.src='{{ theme_asset('assets/img/image-place-holder.png') }}'" onclick="showInstaImage('{{asset("storage/app/public/delivery-man/verification-image/".$image->image)}}')">
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    @else
                                        <p class="__text-414141">
                                    <span>
                                        {{translate('delivery_service_name')}} : {{$order->delivery_service_name}}
                                    </span>
                                            <br>
                                            <span>
                                        {{translate('tracking_id')}} : {{$order->third_party_delivery_tracking_id}}
                                    </span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($order->order_note !=null)
                            <div class="p-2">

                                <h4>{{translate('order_note')}}</h4>
                                <hr>
                                <div class="m-2">
                                    <p>
                                        {{$order->order_note}}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                </div>

                {{-- Modal --}}
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="card-header">
                                {{translate('write_something')}}
                            </div>
                            <div class="modal-body">
                                <form action="{{route('messages_store')}}" method="post" id="chat-form">
                                    @csrf
                                    <input value="{{$order->delivery_man_id}}" name="delivery_man_id" hidden>

                                    <textarea name="message" class="form-control" required></textarea>
                                    <br>
                                    <button class="btn btn--primary" style="color: white;">{{translate('send')}}</button>
                                </form>
                            </div>
                            <div class="card-footer">
                                <a href="{{route('chat', ['type' => 'delivery-man'])}}" class="btn btn--primary mx-1">
                                    {{translate('go_to_chatbox')}}
                                </a>
                                <button type="button" class="btn btn-secondary pull-right" data-dismiss="modal">{{translate('close')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{--Calculation--}}
                <div class="row d-flex justify-content-end">
                    <div class="col-md-8 col-lg-5">
                        <table class="table table-borderless">
                            <tbody class="totals">
                            <tr>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"><span
                                            class="product-qty ">{{translate('item')}}</span></div>
                                </td>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                        <span>{{$order->details->count()}}</span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"><span
                                            class="product-qty ">{{translate('subtotal')}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                        <span>{{\App\CPU\Helpers::currency_converter($summary['subtotal'])}}</span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"><span
                                            class="product-qty ">{{translate('tax_fee')}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                        <span>{{\App\CPU\Helpers::currency_converter($summary['total_tax'])}}</span>
                                    </div>
                                </td>
                            </tr>
                            @if($order->order_type == 'default_type')
                                <tr>
                                    <td>
                                        <div
                                            class="text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"><span
                                                class="product-qty ">{{translate('shipping_Fee')}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                            <span>{{\App\CPU\Helpers::currency_converter($summary['total_shipping_cost'] - ($order['is_shipping_free'] ? $order['extra_discount'] : 0))}}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"><span
                                            class="product-qty ">{{translate('discount_on_product')}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                        <span>- {{\App\CPU\Helpers::currency_converter($summary['total_discount_on_product'])}}</span>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"><span
                                            class="product-qty ">{{translate('coupon_discount')}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                        <span>- {{\App\CPU\Helpers::currency_converter($order->discount_amount)}}</span>
                                    </div>
                                </td>
                            </tr>

                            @if($order->order_type != 'default_type')
                                <tr>
                                    <td>
                                        <div
                                            class="text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"><span
                                                class="product-qty ">{{translate('extra_discount')}}</span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="text-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                            <span>- {{\App\CPU\Helpers::currency_converter($extra_discount)}}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            <tr class="border-top border-bottom">
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}"><span
                                            class="font-weight-bold">{{translate('total')}}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}"><span
                                            class="font-weight-bold amount ">{{\App\CPU\Helpers::currency_converter($order->order_amount)}}</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="justify-content mt-4 for-mobile-glaxy __gap-6px flex-nowrap">
                    <a href="{{route('generate-invoice',[$order->id])}}" class="btn btn--primary for-glaxy-mobile w-50">
                        {{translate('generate_invoice')}}
                    </a>
                    <a class="btn btn-secondary text-white w-49" type="button"
                       href="{{route('track-order.result',['order_id'=>$order['id'],'from_order_details'=>1])}}">
                        {{translate('track_Order')}}
                    </a>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade rtl" id="show-modal-view" tabindex="-1" role="dialog" aria-labelledby="show-modal-image"
        aria-hidden="true" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body flex justify-content-center">
                    <button class="btn btn-default __inline-33" style="{{Session::get('direction') === "rtl" ? 'left' : 'right'}}: -7px;"
                            data-dismiss="modal">
                        <i class="fa fa-close"></i>
                    </button>
                    <img class="element-center" id="attachment-view" src="">
                </div>
            </div>
        </div>
    </div>

@endsection


@push('script')
    <script>
        function review_message() {
            toastr.info('{{translate('you_can_review_after_the_product_is_delivered!')}}', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        function refund_message() {
            toastr.info('{{translate('you_can_refund_request_after_the_product_is_delivered!')}}', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
    <script>
        $('#chat-form').on('submit', function (e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            $.ajax({
                type: "post",
                url: '{{route('messages_store')}}',
                data: $('#chat-form').serialize(),
                success: function (respons) {

                    toastr.success('{{translate('send_successfully')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    $('#chat-form').trigger('reset');
                }
            });

        });

        function showInstaImage(link) {
            $("#attachment-view").attr("src", link);
            $('#show-modal-view').modal('toggle')
        }
    </script>

    <script>
        function digital_product_download(link)
        {
            $.ajax({
                type: "GET",
                url: link,
                responseType: 'blob',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    if (data.status == 1 && data.file_path) {
                        const a = document.createElement('a');
                        a.href = data.file_path;
                        a.download = data.file_name;
                        a.style.display = 'none';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(data.file_path);

                    }
                },
                error: function () {

                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }
    </script>
@endpush

