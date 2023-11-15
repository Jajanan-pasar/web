@extends('layouts.front-end.app')

@section('title',translate('My_Support_Tickets'))
@section('content')

    <div class="modal fade rtl" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" id="open-ticket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg  " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-md-12"><h5
                                class="modal-title font-nameA ">{{translate('submit_new_ticket')}}</h5></div>
                        <div class="col-md-12 text-black mt-3">
                            <span>{{translate('you_will_get_response')}}.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <form class="mt-3" method="post" action="{{route('ticket-submit')}}" id="open-ticket">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="firstName">{{translate('subject')}}</label>
                                <input type="text" class="form-control" id="ticket-subject" name="ticket_subject"
                                       required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <div class="">
                                    <label class="" for="inlineFormCustomSelect">{{translate('type')}}</label>
                                    <select class="custom-select " id="ticket-type" name="ticket_type" required>
                                        <option
                                            value="Website problem">{{translate('website_problem')}}</option>
                                        <option value="Partner request">{{translate('partner_request')}}</option>
                                        <option value="Complaint">{{translate('complaint')}}</option>
                                        <option
                                            value="Info inquiry">{{translate('info_inquiry')}} </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="">
                                    <label class="" for="inlineFormCustomSelect">{{translate('priority')}}</label>
                                    <select class="form-control custom-select" id="ticket-priority"
                                            name="ticket_priority" required>
                                        <option value>{{translate('choose_priority')}}</option>
                                        <option value="Urgent">{{translate('urgent')}}</option>
                                        <option value="High">{{translate('high')}}</option>
                                        <option value="Medium">{{translate('medium')}}</option>
                                        <option value="Low">{{translate('low')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="detaaddressils">{{translate('describe_your_issue')}}</label>
                                <textarea class="form-control" rows="6" id="ticket-description"
                                          name="ticket_description"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer p-0 border-0">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{translate('close')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('submit_a_ticket')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Title-->
    <div class="container rtl">
        <h3 class="headerTitle text-center py-3 mb-0">{{translate('support_ticket')}}</h3>
    </div>
    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row g-3">
            <!-- Sidebar-->
        @include('web-views.partials._profile-aside')
        <!-- Content  -->
            <section class="col-lg-9 col-md-9">
                <!-- Toolbar-->
                <!-- Tickets list-->
                @php($allTickets =App\Model\SupportTicket::where('customer_id', auth('customer')->id())->get())
                <div class="card __card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 __ticket-table">
                                <thead class="thead-light">
                                <tr>
                                    <th class="border-t-0">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO ">{{translate('topic')}}</span></div>
                                    </th>
                                    <th class="border-t-0">
                                        <div class="py-2 {{Session::get('direction') === "rtl" ? 'mr-2' : 'ml-2'}}"><span
                                                class="d-block spandHeadO ">{{translate('submition_date')}}</span>
                                        </div>
                                    </th>
                                    <th class="border-t-0">
                                        <div class="py-2"><span class="d-block spandHeadO">{{translate('type')}}</span>
                                        </div>
                                    </th>
                                    <th class="border-t-0">
                                        <div class="py-2">
                                            <span class="d-block spandHeadO">
                                                {{translate('status')}}
                                            </span>
                                        </div>
                                    </th>
                                    <th class="border-t-0 text-center">
                                        <div class="py-2"><span class="d-block spandHeadO">{{translate('action')}} </span></div>
                                    </th>
                                </tr>
                                </thead>

                                <tbody>

                                @foreach($allTickets as $ticket)
                                    <tr>
                                        <td>
                                            <span class="marl">{{$ticket['subject']}}</span>
                                        </td>
                                        <td>
                                            <span>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$ticket['created_at'])->format('Y-m-d h:i A')}}</span>
                                        </td>
                                        <td><span class="">{{translate($ticket['type'])}}</span></td>
                                        <td><span class="">{{translate($ticket['status'])}}</span></td>
                                        <td>
                                            <div class="btn--container flex-nowrap justify-content-center">
                                                <a class="action-btn btn--primary"
                                                href="{{route('support-ticket.index',$ticket['id'])}}">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a class="action-btn btn--danger" href="javascript:"
                                                onclick="Swal.fire({
                                                    title: '{{translate('do_you_want_to_delete_this')}}?',
                                                    showDenyButton: true,
                                                    showCancelButton: true,
                                                    confirmButtonColor: '{{$web_config['primary_color']}}',
                                                    cancelButtonColor: '{{$web_config['secondary_color']}}',
                                                    confirmButtonText: `{{translate('yes')}}`,
                                                    cancelButtonText: `{{translate('cancel')}}`,
                                                    denyButtonText: `{{translate('Do_not_delete')}}`,
                                                    }).then((result) => {
                                                    if (result.value) {
                                                    Swal.fire(`{{translate('deleted!')}}`, '', 'success')
                                                    location.href='{{ route('support-ticket.delete',['id'=>$ticket->id])}}';
                                                    } else{
                                                    Swal.fire(`{{translate('cancelled')}}`, '', 'info')
                                                    }
                                                    })"
                                                id="delete" class=" marl">
                                                    <i class="czi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn--primary float-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}" data-toggle="modal"
                            data-target="#open-ticket">
                            {{translate('add_new_ticket')}}
                    </button>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('script')
@endpush
