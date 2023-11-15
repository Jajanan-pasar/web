<div class="feature_header mb-2">
    <span>{{ translate('shopping_cart')}}</span>
</div>

@php($shippingMethod=\App\CPU\Helpers::get_business_settings('shipping_method'))
@php($cart=\App\Model\Cart::where(['customer_id' => (auth('customer')->check() ? auth('customer')->id() : session('guest_id'))])->get()->groupBy('cart_group_id'))

<div class="row g-3">
    <!-- List of items-->
    <section class="col-lg-8">
            @if(count($cart)==0)
                @php($physical_product = false)
            @endif

            @foreach($cart as $group_key=>$group)
            <div class="card __card cart_information mb-3">
                @foreach($group as $cart_key=>$cartItem)
                    @if ($shippingMethod=='inhouse_shipping')
                            <?php

                            $admin_shipping = \App\Model\ShippingType::where('seller_id', 0)->first();
                            $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';

                            ?>
                    @else
                            <?php
                            if ($cartItem->seller_is == 'admin') {
                                $admin_shipping = \App\Model\ShippingType::where('seller_id', 0)->first();
                                $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
                            } else {
                                $seller_shipping = \App\Model\ShippingType::where('seller_id', $cartItem->seller_id)->first();
                                $shipping_type = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
                            }
                            ?>
                    @endif

                    @if($cart_key==0)
                        <div class="card-header">
                            @php($verify_status = \App\CPU\OrderManager::minimum_order_amount_verify($request, $group_key))

                            @if($cartItem->seller_is=='admin')
                                <b>
                                    <span>{{ translate('shop_name')}} : </span>
                                    <a href="{{route('shopView',['id'=>0])}}">{{\App\CPU\Helpers::get_business_settings('company_name')}}</a>
                                </b>
                            @else
                                <b>
                                    <span>{{ translate('shop_name')}}:</span>
                                    <a href="{{route('shopView',['id'=>$cartItem->seller_id])}}">
                                        {{\App\Model\Shop::where(['seller_id'=>$cartItem['seller_id']])->first()->name}}
                                    </a>
                                </b>
                            @endif

                            @if ($verify_status['minimum_order_amount'] > $verify_status['amount'])
                            <span class="pl-1 text-danger pulse-button" data-toggle="tooltip" data-placement="right"
                                onclick="minimum_Order_Amount_message(this.getAttribute('data-title'))"
                                data-title="{{ translate('minimum_Order_Amount') }} {{ \App\CPU\Helpers::currency_converter($verify_status['minimum_order_amount']) }} {{ translate('for') }} @if($cartItem->seller_is=='admin') {{\App\CPU\Helpers::get_business_settings('company_name')}} @else {{ \App\CPU\get_shop_name($cartItem['seller_id']) }} @endif" title="{{ translate('minimum_Order_Amount') }} {{ \App\CPU\Helpers::currency_converter($verify_status['minimum_order_amount']) }} {{ translate('for') }} @if($cartItem->seller_is=='admin') {{\App\CPU\Helpers::get_business_settings('company_name')}} @else {{ \App\CPU\get_shop_name($cartItem['seller_id']) }} @endif">
                                <i class="czi-security-announcement"></i>
                            </span>
                            @endif
                        </div>
                    @endif
                @endforeach
                <div class="table-responsive mt-3">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table __cart-table">
                        <thead class="thead-light">
                            <tr class="">
                                <th class="font-weight-bold __w-5p">{{translate('SL#')}}</th>
                                @if ( $shipping_type != 'order_wise')
                                <th class="font-weight-bold __w-30p">{{translate('product_details')}}</th>
                                @else
                                <th class="font-weight-bold __w-45">{{translate('product_details')}}</th>
                                @endif
                                <th class="font-weight-bold __w-15p">{{translate('unit_price')}}</th>
                                <th class="font-weight-bold __w-15p">{{translate('qty')}}</th>
                                <th class="font-weight-bold __w-15p">{{translate('price')}}</th>
                                @if ( $shipping_type != 'order_wise')
                                    <th class="font-weight-bold __w-15p">{{translate('shipping_cost')}} </th>
                                @endif
                                <th class="font-weight-bold __w-5p"></th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                            $physical_product = false;
                            foreach ($group as $row) {
                                if ($row->product_type == 'physical') {
                                    $physical_product = true;
                                }
                            }
                        ?>
                        @foreach($group as $cart_key=>$cartItem)
                        @php($product_status = $cartItem->all_product->status)
                            <tr>
                                <td>{{$cart_key+1}}</td>
                                <td>
                                    <div class="d-flex">
                                        <div class="__w-30p">
                                            <a href="{{ $product_status == 1 ? route('product',$cartItem['slug']) : 'javascript:'}}" class="position-relative overflow-hidden">
                                                <img class="rounded __img-62 {{ $product_status == 0?'blur-section':'' }}"
                                                        onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                        src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$cartItem['thumbnail']}}"
                                                        alt="Product">
                                                @if ($product_status == 0)
                                                <span class="temporary-closed position-absolute text-center p-2">
                                                    <span>{{ translate('N/A') }}</span>
                                                </span>
                                                @endif
                                            </a>
                                        </div>
                                        <div class="ml-2 text-break __line-2 __w-70p {{ $product_status == 0?'blur-section':'' }}">
                                            <a href="{{ $product_status == 1 ? route('product',$cartItem['slug']) : 'javascript:'}}">{{$cartItem['name']}}</a>
                                        </div>
                                    </div>
                                    <div class="d-flex {{ $product_status == 0?'blur-section':'' }}">

                                        @foreach(json_decode($cartItem['variations'],true) as $key1 =>$variation)
                                            <div class="text-muted mr-2">
                                                <span class="{{Session::get('direction') === "rtl" ? 'ml-2' : 'mr-2'}} __text-12px">
                                                    {{$key1}} : {{$variation}}</span>

                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="{{ $product_status == 0?'blur-section':'' }}">
                                    <div class=" text-accent">{{ \App\CPU\Helpers::currency_converter($cartItem['price']-$cartItem['discount']) }}</div>
                                        @if($cartItem['discount'] > 0)
                                            <strike class="__inline-18">
                                                {{\App\CPU\Helpers::currency_converter($cartItem['price'])}}
                                            </strike>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="{{ $product_status == 0?'blur-section':'' }}">
                                        @php($minimum_order=\App\Model\Product::select('minimum_order_qty')->find($cartItem['product_id']))
                                        <input class="__cart-input" type="number" name="quantity[{{ $cartItem['id'] }}]" id="cartQuantity{{$cartItem['id']}}" {{ $product_status == 0?'disabled':'' }}
                                        onchange="updateCartQuantity('{{ $minimum_order->minimum_order_qty }}', '{{$cartItem['id']}}')" min="{{ $minimum_order->minimum_order_qty ?? 1 }}" value="{{$cartItem['quantity']}}">
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ \App\CPU\Helpers::currency_converter(($cartItem['price']-$cartItem['discount'])*$cartItem['quantity']) }}
                                    </div>
                                </td>
                                <td>
                                    @if ( $shipping_type != 'order_wise')
                                        {{ \App\CPU\Helpers::currency_converter($cartItem['shipping_cost']) }}
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-link px-0 text-danger"
                                            onclick="removeFromCart({{ $cartItem['id'] }})" type="button"><i
                                            class="czi-close-circle {{Session::get('direction') === "rtl" ? 'ml-2' : 'mr-2'}}"></i>
                                    </button>
                                </td>
                                </tr>

                                @if($physical_product && $shippingMethod=='sellerwise_shipping' && $shipping_type == 'order_wise')
                                    @php($choosen_shipping=\App\Model\CartShipping::where(['cart_group_id'=>$cartItem['cart_group_id']])->first())

                                    @if(isset($choosen_shipping)==false)
                                        @php($choosen_shipping['shipping_method_id']=0)
                                    @endif

                                    @php($shippings=\App\CPU\Helpers::get_shipping_methods($cartItem['seller_id'],$cartItem['seller_is']))
                                    <tr>
                                        <td colspan="4">

                                            @if($cart_key==$group->count()-1)

                                                <!-- choosen shipping method-->

                                                <div class="row">

                                                    <div class="col-12">
                                                        <select class="form-control"
                                                                onchange="set_shipping_id(this.value,'{{$cartItem['cart_group_id']}}')">
                                                            <option>{{translate('choose_shipping_method')}}</option>
                                                            @foreach($shippings as $shipping)
                                                                <option
                                                                    value="{{$shipping['id']}}" {{$choosen_shipping['shipping_method_id']==$shipping['id']?'selected':''}}>
                                                                    {{$shipping['title'].' ( '.$shipping['duration'].' ) '.\App\CPU\Helpers::currency_converter($shipping['cost'])}}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                    @endif
                                </td>
                                <td colspan="3">
                                    @if($cart_key==$group->count()-1)
                                    <div class="row">
                                        <div class="col-12">
                                            <span>
                                                <b>{{translate('shipping_cost')}} : </b>
                                            </span>
                                            {{\App\CPU\Helpers::currency_converter($choosen_shipping['shipping_method_id']!= 0?$choosen_shipping->shipping_cost:0)}}
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @php($free_delivery_status = \App\CPU\OrderManager::free_delivery_order_amount($group[0]->cart_group_id))

                @if ($free_delivery_status['status'] && (session()->missing('coupon_type') || session('coupon_type') !='free_delivery'))
                <div class="free-delivery-area px-3">
                    <div class="d-flex align-items-center gap-8">
                        <img src="{{ asset('public/assets/front-end/img/icons/free-shipping.png') }}" alt="" width="40">
                        @if ($free_delivery_status['amount_need'] <= 0)
                            <span class="text-muted fs-16">{{ translate('you_Get_Free_Delivery_Bonus') }}</span>
                        @else
                        <span class="need-for-free-delivery font-bold">{{ \App\CPU\Helpers::currency_converter($free_delivery_status['amount_need']) }}</span>
                            <span class="text-muted fs-16">{{ translate('add_more_for_free_delivery') }}</span>
                        @endif
                    </div>
                    <div class="progress free-delivery-progress">
                        <div class="progress-bar" role="progressbar" style="width: {{ $free_delivery_status['percentage'] }}%" aria-valuenow="{{ $free_delivery_status['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                @endif



            </div>
            @endforeach

@if($shippingMethod=='inhouse_shipping')
        <?php
            $physical_product = false;
            foreach($cart as $group_key=>$group){
                foreach ($group as $row) {
                    if ($row->product_type == 'physical') {
                        $physical_product = true;
                    }
                }
            }
        ?>

        <?php
            $admin_shipping = \App\Model\ShippingType::where('seller_id', 0)->first();
            $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
        ?>
    @if ($shipping_type == 'order_wise' && $physical_product)
        @php($shippings=\App\CPU\Helpers::get_shipping_methods(1,'admin'))
        @php($choosen_shipping=\App\Model\CartShipping::where(['cart_group_id'=>$cartItem['cart_group_id']])->first())

        @if(isset($choosen_shipping)==false)
            @php($choosen_shipping['shipping_method_id']=0)
        @endif
        <div class="row">
            <div class="col-12">
                <select class="form-control" onchange="set_shipping_id(this.value,'all_cart_group')">
                    <option>{{translate('choose_shipping_method')}}</option>
                    @foreach($shippings as $shipping)
                        <option
                            value="{{$shipping['id']}}" {{$choosen_shipping['shipping_method_id']==$shipping['id']?'selected':''}}>
                            {{$shipping['title'].' ( '.$shipping['duration'].' ) '.\App\CPU\Helpers::currency_converter($shipping['cost'])}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
@endif

@if( $cart->count() == 0)
    <div class="d-flex justify-content-center align-items-center">
        <h4 class="text-danger text-capitalize">{{translate('cart_empty')}}</h4>
    </div>
@endif


        <form  method="get">
            <div class="form-group">
                <div class="row">
                    <div class="col-12">
                        <label for="phoneLabel" class="form-label input-label">{{translate('order_note')}} <span
                                            class="input-label-secondary">({{translate('optional')}})</span></label>
                        <textarea class="form-control w-100" id="order_note" name="order_note">{{ session('order_note')}}</textarea>
                    </div>
                </div>
            </div>
        </form>


        <div class="d-flex btn-full-max-sm align-items-center __gap-6px flex-wrap justify-content-between">
            <a href="{{route('home')}}" class="btn btn--primary">
                <i class="fa fa-{{Session::get('direction') === "rtl" ? 'forward' : 'backward'}} px-1"></i> {{translate('continue_shopping')}}
            </a>
            <a onclick="checkout()"
            class="btn btn--primary pull-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                {{translate('checkout')}}
                <i class="fa fa-{{Session::get('direction') === "rtl" ? 'backward' : 'forward'}} px-1"></i>
            </a>
        </div>
</section>
<!-- Sidebar-->
@include('web-views.partials._order-summary')
</div>


<script>
    cartQuantityInitialize();

    function set_shipping_id(id, cart_group_id) {
        $.get({
            url: '{{url('/')}}/customer/set-shipping-method',
            dataType: 'json',
            data: {
                id: id,
                cart_group_id: cart_group_id
            },
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                location.reload();
            },
            complete: function () {
                $('#loading').hide();
            },
        });
    }
</script>
<script>
    function checkout() {
        let order_note = $('#order_note').val();
        //console.log(order_note);
        $.post({
            url: "{{route('order_note')}}",
            data: {
                _token: '{{csrf_token()}}',
                order_note: order_note,

            },
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                let url = "{{ route('checkout-details') }}";
                location.href = url;

            },
            complete: function () {
                $('#loading').hide();
            },
        });
    }

    function minimum_Order_Amount_message(data)
    {
        toastr.error(data, {
            CloseButton: true,
            ProgressBar: true
        });
    }

</script>
