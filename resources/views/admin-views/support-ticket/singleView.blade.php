@extends('layouts.back-end.app')

@section('title', translate('support_Ticket'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/support_ticket.png')}}" alt="">
                {{translate('support_ticket')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Support ticket inbox -->
        <div class="card card-chat justify-content-between">
            <!--  Message Header -->
            <div class="card-header flex-wrap gap-3">
                @foreach($supportTicket as $ticket )
                <?php
                $userDetails = \App\User::where('id', $ticket['customer_id'])->first();
                $conversations = \App\Model\SupportTicketConv::where('support_ticket_id', $ticket['id'])->orderBy('id','desc')->get();
                $admin = \App\Model\Admin::get();
                ?>
                <div class="media d-flex gap-3">
                    <img class="rounded-circle avatar" src="{{asset('storage/app/public/profile')}}/{{isset($userDetails)?$userDetails['image']:''}}"
                            onerror="this.src='{{asset('public/assets/back-end/img/image-place-holder.png')}}'"
                            alt="{{isset($userDetails)?$userDetails['name']:'not found'}}"/>
                    <div class="media-body">
                        <h6 class="font-size-md mb-1">{{isset($userDetails)?$userDetails['f_name'].' '.$userDetails['l_name']:'not found'}}</h6>
                        <div class="fz-12">{{isset($userDetails)?$userDetails['phone']:''}}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div class="type font-weight-bold bg-soft--primary c1 px-2 rounded">{{translate(str_replace('_',' ',$ticket['type']))}}</div>
                    <div class="priority d-flex flex-wrap align-items-center gap-3">
                        <span class="title-color">{{translate('priority')}}:</span>
                        <span class="font-weight-bold badge-soft-info rounded px-2">{{translate(str_replace('_',' ',$ticket['priority']))}}</span>
                    </div>
                </div>
                @endforeach

                <!-- End Profile -->
            </div>
            <!-- End Inbox Message Header -->

            <div class="messaging">
                <div class="inbox_msg gap-3 px-3 mb-2 py-2 rounded">
                    <!-- Message Body -->
                    <div class="mesgs">
                        <div class="msg_history-53 d-flex flex-column-reverse">
                            @foreach($conversations as $conversation)
                            @if($conversation['admin_message'] ==null )
                                <div class="mb-2">
                                    <p class="font-size-md message-box message-box_incoming mb-1">{{$conversation['customer_message']}}</p>
                                    <span class="fz-12 mx-1 text-muted d-flex">
                                        @if ($conversation->created_at->diffInDays(\Carbon\Carbon::now()) < 7)
                                            {{ date('D h:i:A',strtotime($conversation->created_at)) }}
                                        @else
                                            {{ date('d M Y h:i:A',strtotime($conversation->created_at)) }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                            @if($conversation['customer_message'] ==null )
                                <div class="mb-2 mx-2 d-flex flex-column align-items-end">
                                    <div>
                                        <p class="font-size-md message-box mb-1">{{$conversation['admin_message']}}</p>
                                        <span class="fz-12 mx-1 text-muted d-flex">
                                            @if ($conversation['created_at']->diffInDays(\Carbon\Carbon::now()) < 7)
                                                {{ date('D h:i:A',strtotime($conversation->created_at)) }}
                                            @else
                                                {{ date('d M Y h:i:A',strtotime($conversation->created_at)) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                            @endforeach
                            <div class="mb-2">
                                <p class="font-size-md message-box message-box_incoming mb-1">{{$ticket['description']}}</p>
                                <span class="fz-12 mx-1 text-muted d-flex">
                                    @if ($ticket->created_at->diffInDays(\Carbon\Carbon::now()) < 7)
                                        {{ date('D h:i:A',strtotime($ticket->created_at)) }}
                                    @else
                                        {{ date('d M Y h:i:A',strtotime($ticket->created_at)) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <h5 class="pt-2 pb-1 d-flex mx-1">{{translate('leave_a_Message')}}</h5>
                        @foreach($supportTicket as $reply)
                            <form class="needs-validation" href="{{route('admin.support-ticket.replay',$reply['id'])}}" method="post"
                                >
                                @csrf
                                <input type="hidden" name="id" value="{{$reply['id']}}">
                                <input type="hidden" name="adminId" value="1">
                                <div class="form-group">
                                <textarea class="form-control h-120" name="replay" rows="8" placeholder="{{translate('write_your_message_here')}}..."
                                        required></textarea>
                                    <div class="invalid-tooltip">{{translate('please write the message')}}!</div>
                                </div>
                                <div class="d-flex flex-wrap justify-content-between align-items-center">
                                    <div class="custom-control custom-checkbox d-block">
                                    </div>
                                    <button class="btn btn--primary px-4" type="submit">{{translate('reply')}}</button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                    <!-- End Message Body -->
                </div>
            </div>
        </div>
        <!-- end-->
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{asset('public/assets/back-end')}}/js/demo/datatables-demo.js"></script>
    <script src="{{asset('public/assets/back-end/js/croppie.js')}}"></script>

@endpush
