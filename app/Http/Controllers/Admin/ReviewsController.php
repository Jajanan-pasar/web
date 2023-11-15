<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\CPU\Helpers;
use App\Model\Review;
use App\Model\Product;
use App\CPU\ProductManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class ReviewsController extends Controller
{
    function list(Request $request)
    {
        $query_param = [];
        if (!empty($request->from) && empty($request->to)) {
            Toastr::warning(translate('please_select_to_date'));
        }
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $product_id = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $customer_id = User::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $reviews = Review::WhereIn('product_id',  $product_id)->orWhereIn('customer_id', $customer_id);
            $query_param = ['search' => $request['search']];
        } else {
            $reviews = Review::with(['product', 'customer'])
                ->when($request->product_id != 0, function ($q) {
                    $q->where('product_id', request('product_id'));
                })->when($request->customer_id != 'all' && $request->customer_id != null, function ($q) {
                    $q->where('customer_id', request('customer_id'));
                })->when($request->status != null, function ($q) {
                    $q->where('status', request('status'));
                })->when($request->from && $request->to, function ($q) use ($request) {
                    $q->whereBetween('created_at', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
                });
        }
        $reviews = $reviews->whereNull('delivery_man_id')
                            ->latest('created_at')
                            ->paginate(Helpers::pagination_limit())
                            ->appends([
                                'search' => $request['search'],
                                'product_id'=>$request['product_id'],
                                'customer_id',$request->customer_id,
                                'status'=>$request->status,
                                'from'=>$request->from,
                                'to'=>$request->to
                                ]);;
        $products = Product::whereNotIn('request_status',[0])->select('id', 'added_by', 'user_id','name','category_id', 'brand_id', 'thumbnail')->get();
        $product = Product::find($request->product_id);
        $customer = "all";
        if($request->customer_id != 'all' && !is_null($request->customer_id) && $request->has('customer_id')){
            $customer =User::find($request->customer_id);
        }
        $customer_id = $request['customer_id'];
        $product_id = $request['product_id'];
        $status = $request['status'];
        $from = $request->from;
        $to = $request->to;

        return view('admin-views.reviews.list', compact('reviews', 'search', 'products', 'product','customer', 'from', 'to', 'customer_id', 'product_id', 'status'));
    }
    public function export(Request $request)
    {
        $product_id = $request['product_id'];
        $customer_id = $request['customer_id'];
        $status = $request['status'];
        $from = $request['from'];
        $to = $request['to'];

        if ($request->has('search') && $request->search != '') {
            $key = explode(' ', $request['search']);
            $product_id = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $customer_id = User::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%");
                }
            })->pluck('id')->toArray();
            $data = Review::WhereIn('product_id',  $product_id)->orWhereIn('customer_id', $customer_id)->get();
        } else {
            $data = Review::with(['product', 'customer'])
                    ->when($product_id != null, function ($q) use ($request) {
                        $q->where('product_id', $request['product_id']);
                    })
                    ->when($customer_id != 'all' , function ($q) use ($request) {
                            $q->where('customer_id', $request['customer_id']);
                    })
                    ->when($status != null, function ($q) use ($request) {
                            $q->where('status', $request['status']);
                    })
                    ->when($to != null && $from != null, function ($query) use ($from, $to) {
                        $query->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                    })->get();
        }

        if($data->count()==0){
            Toastr::warning(translate('no_data_found_for_export'));
            return back();
        }

        return (new FastExcel(ProductManager::export_product_reviews($data)))->download('Review' . date('d_M_Y') . '.xlsx');
    }
    public function status(Request $request)
    {
        $review = Review::find($request->id);
        $review->status = $request->status ?? 0;
        $review->save();

        if ($request->ajax()) {
            return response()->json([
                'status' => 1,
                'message' => translate('review_status_updated.')
            ]);
        }

        Toastr::success(translate('review_status_updated'));
        return back();
    }
    public function get_customers(Request $request){
        $key = explode(' ', $request['q']);
        $data = DB::table('users')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            })
            ->whereNotNull(['f_name', 'l_name', 'phone'])
            ->limit(20)
            ->get([DB::raw('id,IF(id <> "0", CONCAT(f_name, " ", l_name, " (", phone ,")"),CONCAT(f_name, " ", l_name)) as text')]);

        return response()->json($data);

    }
    /**
     * Search product
     */
    public function search_product(Request $request){
        $key = explode(' ', $request['name']);
        $products = Product::active()->with(['brand','category','seller.shop'])->where(function ($query) use ($key) {
            foreach ($key as $value) {
                $query->where('name', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'result' => view('admin-views.partials._search-product', compact('products'))->render(),
        ]);
    }
}
