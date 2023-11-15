<div class="modal fade" id="refundModal{{$id}}" tabindex="-1" aria-labelledby="refundRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <H6 class="text-center text-capitalize">{{translate('refund_request')}}</H6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body d-flex flex-column gap-3">
                <div class="border rounded bg-white">
                    <div class="p-3">
                        <div class="media gap-3">
                            @if (isset($order_details->product))
                                <div class="position-relative">
                                    <img class="d-block" onclick="location.href='{{route('product',$order_details->product['slug'])}}'"
                                    onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                    src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$order_details->product['thumbnail']}}"
                                    alt="VR Collection" width="100">

                                    @if($order_details->product->discount > 0)
                                        <span class="price-discount badge badge-primary position-absolute top-1 left-1">
                                                @if ($order_details->product->discount_type == 'percent')
                                                    {{round($order_details->product->discount)}}%
                                                @elseif($order_details->product->discount_type =='flat')
                                                    {{\App\CPU\Helpers::currency_converter($order_details->product->discount)}}
                                                @endif
                                        </span>
                                    @endif
                                </div>
                                <div class="media-body">

                                    <a href="{{route('product',[$order_details->product['slug']])}}">
                                        <h6 class="mb-1">
                                            {{Str::limit($product['name'],40)}}
                                        </h6>
                                    </a>
                                    @if($order_details->variant)
                                        <div><small class="text-muted">{{translate('variant')}} : {{$detail->variant}}</small></div>
                                    @endif

                                    <div><small class="text-muted">{{translate('qty')}} : {{$detail->qty}}</small></div>
                                    <div><small class="text-muted">{{translate('price')}} : <span class="text-primary">
                                        {{\App\CPU\Helpers::currency_converter($order_details->price)}}
                                        </span></small>
                                    </div>
                                </div>
                            @else
                                <div class="media-body">
                                    <h6 class="mb-1">{{translate('product_not_found')}}</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="border rounded bg-white">
                    <div class="p-3 fs-12 d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between gap-2">
                            <div class="text-muted text-capitalize">{{translate('total_price')}}</div>
                            <div>{{\App\CPU\Helpers::currency_converter($order_details->price)}}</div>
                        </div>
                        <div class="d-flex justify-content-between gap-2">
                            <div class="text-muted text-capitalize">{{translate('product_discount')}}</div>
                            <div>-{{\App\CPU\Helpers::currency_converter($order_details->discount)}}</div>
                        </div>
                        <div class="d-flex justify-content-between gap-2">
                            <div class="text-muted">vat/tax</div>
                            <div>{{\App\CPU\Helpers::currency_converter($order_details->tax)}}</div>
                        </div>
                        <?php
                            $total_product_price = 0;
                            foreach ($order->details as $key => $or_d) {
                                $total_product_price += ($or_d->qty*$or_d->price) + $or_d->tax - $or_d->discount;
                            }
                            $refund_amount = 0;
                            $subtotal = ($order_details->price * $order_details->qty) - $order_details->discount + $order_details->tax;

                            $coupon_discount = ($order->discount_amount*$subtotal)/$total_product_price;

                            $refund_amount = $subtotal - $coupon_discount;
                        ?>
                        <div class="d-flex justify-content-between gap-2">
                            <div class="text-muted text-capitalize">{{translate('sub_total')}}</div>
                            <div>{{\App\CPU\Helpers::currency_converter($subtotal)}}</div>
                        </div>
                        <div class="d-flex justify-content-between gap-2">
                            <div class="text-muted text-capitalize">{{translate('coupon_discount')}}</div>
                            <div> -{{\App\CPU\Helpers::currency_converter($coupon_discount)}}</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between gap-2 border-top py-2 px-3 fs-12">
                        <div class="text-muted font-weight-bold text-capitalize">{{translate('total_refundable_amount')}}</div>
                        <div class="font-weight-bold">{{\App\CPU\Helpers::currency_converter($refund_amount)}}</div>
                    </div>
                </div>
                <!-- form submit  -->
                <form action="{{route('refund-store')}}"  method="post" enctype="multipart/form-data">
                    @csrf
                    <h6 class="d-flex gap-2 align-items-center cursor-pointer" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        {{translate('give_a_refund_reason')}} <i class="tio-chevron-down"></i>
                    </h6>
                    <div class="collapse" id="collapseExample">
                        <input type="hidden" name="order_details_id" value="{{$order_details->id}}">
                        <input type="hidden" name="amount" value="{{$refund_amount}}">
                        <textarea rows="4" class="form-control" name="refund_reason" placeholder="{{translate('write_here')}}..." required></textarea>
                    </div>

                    <div class="mt-3">
                        <h6>{{translate('upload_images')}}</h6>

                        <div class="mt-2">
                            <div class="mt-2">
                                <div class="d-flex gap-2 flex-wrap">
                                    <div class="d-flex gap-4 flex-wrap coba_refund"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn--primary text-capitalize">{{translate('send_request')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
