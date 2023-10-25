@extends('backEnd.master')
@section('title') 
    @lang('communicate.calendar')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('public/backEnd/assets/vendors/css/fullcalendar.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/backEnd/assets/vendors/css/customFullCalendar.css') }}" />
    <style>
        .color-input {
    height: 50px;
    padding: 0px !important;
    border: none !important;
    background: transparent;
}

@media(max-width: 576px){
    .fc-daygrid-day-frame {
        padding-bottom: 20px;
    }
    a.fc-daygrid-more-link.fc-more-link {
        font-size: 8px;
    }

    .fc-button-group{
        margin-bottom: 20px
    }
}
    </style>
@endpush

@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('communicate.calendar')</h1>
            <div class="bc-pages">
                <input type="hidden" id="system_url" value="{{url('/')}}">
                <a href="{{route('dashboard')}}">@lang('common.dashboard')</a>
                <a href="#">@lang('communicate.communicate')</a>
                <a href="#">@lang('communicate.calendar')</a>
            </div>
        </div>
    </div>
</section>

<section class="mb-40 sms-accordion">
    <div class="container-fluid p-0">
        @if(userPermission('academic-calendar-settings-view'))
            <div class="row">
                <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                    <a href="#" class="primary-btn small fix-gr-bg calenderSettingsJs">
                        <span class="ti-plus pr-2"></span>
                        @lang('communicate.calendar_settings')
                    </a>
                </div>
            </div>
        @endif

        @include('backEnd.communicate._calendarSettingsForm')
        
        <div class="row">
            <div class="col-lg-4 col-md-6 col-5">
                <div class="main-title">
                    <h3 class="mb-30">@lang('communicate.calendar')</h3>
                </div>
            </div>
        </div>
        <div class="row">
              <div class="col-lg-12">
                <div class="white-box">
                  <div id='academicCalendar'></div>
              </div>
          </div>
        </div>

        <div class="modal fade admin-query" id="descriptionModal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@lang('common.description')</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="text-center">
                            <div class="admissionQueryModal commonModalContent">
                                <h4>@lang('admin.admission_query')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('admin.name')</th>
                                            <th scope="col" id="AQname"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('admin.phone')</th>
                                            <th scope="col" id="AQphone"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('admin.email')</th>
                                            <th scope="col" id="AQemail"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('admin.address')</th>
                                            <th scope="col" id="AQaddress"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('admin.next_follow_up_date')</th>
                                            <th scope="col" id="AQdate"></th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="mt-20 eventActionCutomButton">
                                    <div class="col-lg-12 text-center">
                                        <a class="primary-btn fix-gr-bg" target="_blank" id="admissionQueryUrl">
                                            <span class="pl ti-link"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="homeworkModal commonModalContent">
                                <h4>@lang('communicate.homework')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('common.description')</th>
                                            <th scope="col" id="Hdescription"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('academics.class')</th>
                                            <th scope="col" id="Hclass"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('academics.section')</th>
                                            <th scope="col" id="Hsection"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('homework.subject')</th>
                                            <th scope="col" id="Hsubject"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('communicate.submission_date')</th>
                                            <th scope="col" id="Hdate"></th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="mt-20 eventActionCutomButton">
                                    <div class="col-lg-12 text-center">
                                        <a class="primary-btn fix-gr-bg" target="_blank" id="homeworkRoute">
                                            <span class="pl ti-link"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="studyMaterialModal commonModalContent">
                                <h4>@lang('communicate.study_material')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('common.title')</th>
                                            <th scope="col" id="SMtitle"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('study.content_type')</th>
                                            <th scope="col" id="SMtype"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('communicate.avaiable')</th>
                                            <th scope="col" id="SMavailable"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.description')</th>
                                            <th scope="col" id="SMdescription"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.created_at')</th>
                                            <th scope="col" id="SMdate"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="eventModal commonModalContent">
                                <h4>@lang('communicate.event')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('common.title')</th>
                                            <th scope="col" id="Etitle"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.description')</th>
                                            <th scope="col" id="Edescription"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('communicate.location')</th>
                                            <th scope="col" id="Elocation"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.start_date')</th>
                                            <th scope="col" id="Esdate"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.end_date')</th>
                                            <th scope="col" id="Eedate"></th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="mt-20 eventActionCutomButton">
                                    <div class="col-lg-12 text-center">
                                        <a class="primary-btn fix-gr-bg d-none" target="_blank" id="eventFile">
                                            <span class="pl ti-download"></span>
                                        </a>
                                        <a class="primary-btn fix-gr-bg d-none" target="_blank" id="eventLink">
                                            <span class="pl ti-link"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="holidayModal commonModalContent">
                                <h4>@lang('communicate.event')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('common.title')</th>
                                            <th scope="col" id="HDtitle"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.description')</th>
                                            <th scope="col" id="HDdescription"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.start_date')</th>
                                            <th scope="col" id="HDsdate"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.end_date')</th>
                                            <th scope="col" id="HDedate"></th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="mt-20 eventActionCutomButton">
                                    <div class="col-lg-12 text-center">
                                        <a class="primary-btn fix-gr-bg d-none" target="_blank" id="holidayFile">
                                            <span class="pl ti-download"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="examModal commonModalContent">
                                <h4>@lang('communicate.exam')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('exam.exam_type')</th>
                                            <th scope="col" id="EMname"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('academics.class')</th>
                                            <th scope="col" id="EMclass"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('academics.section')</th>
                                            <th scope="col" id="EMsection"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.subject')</th>
                                            <th scope="col" id="EMsubject"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.teacher')</th>
                                            <th scope="col" id="EMteacher"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.room')</th>
                                            <th scope="col" id="EMroom"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.date')</th>
                                            <th scope="col" id="EMdate"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.start_time')</th>
                                            <th scope="col" id="EMstarttime"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.end_time')</th>
                                            <th scope="col" id="EMendtime"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="noticeBoardModal commonModalContent">
                                <h4>@lang('communicate.notice_board')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('common.title')</th>
                                            <th scope="col" id="NBtitle"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.description')</th>
                                            <th scope="col" id="NBdescription"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('communicate.inform_to')</th>
                                            <th scope="col" id="NBinform"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.date')</th>
                                            <th scope="col" id="NBdate"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="onlineExamModal commonModalContent">
                                <h4>@lang('communicate.online_exam')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('exam.title')</th>
                                            <th scope="col" id="OEtitle"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('academics.class')</th>
                                            <th scope="col" id="OEclass"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('academics.section')</th>
                                            <th scope="col" id="OEsection"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.subject')</th>
                                            <th scope="col" id="OEsubject"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.start_date')</th>
                                            <th scope="col" id="OEstartdate"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.end_date')</th>
                                            <th scope="col" id="OEenddate"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.start_time')</th>
                                            <th scope="col" id="OEstarttime"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.end_time')</th>
                                            <th scope="col" id="OEendtime"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="lessonPlanModal commonModalContent">
                                <h4>@lang('communicate.lesson_plan')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('academics.class')</th>
                                            <th scope="col" id="LPclass"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('academics.section')</th>
                                            <th scope="col" id="LPsection"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.subject')</th>
                                            <th scope="col" id="LPsubject"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.teacher')</th>
                                            <th scope="col" id="LPteacher"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.date')</th>
                                            <th scope="col" id="LPdate"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="leaveModal commonModalContent">
                                <h4>@lang('communicate.leave')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('common.name')</th>
                                            <th scope="col" id="Lname"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('leave.reason')</th>
                                            <th scope="col" id="Lreason"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.start_date')</th>
                                            <th scope="col" id="Lstartdate"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('common.end_date')</th>
                                            <th scope="col" id="Lenddate"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                            <div class="libraryModal commonModalContent">
                                <h4>@lang('communicate.library')</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">@lang('library.book_title')</th>
                                            <th scope="col" id="Lbooktitle"></th>
                                        </tr>
                                        <tr>
                                            <th scope="col">@lang('reports.due_date')</th>
                                            <th scope="col" id="Lduedate"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade admin-query" id="addEventOnCalendar">
          <div class="modal-dialog modal-dialog-centered large-modal">
              <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="modal-title">@lang('communicate.add_event') <span id="currentDate"></span></h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                      <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-4">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('communicate.event_title') <span class="text-danger"> *</span> </label>
                                            <input class="primary_input_field form-control{{ $errors->has('event_title') ? ' is-invalid' : '' }} commonInputBlank" type="text" name="event_title" autocomplete="off" value="{{isset($editData)? $editData->event_title : old('event_title') }}">
                                            <span id="error_event_title" class="text-danger commonErrorBlank"></span>
                                        </div>
                                        </div>

                                        <div class="col-lg-4">
                                        <label for="checkbox" class="mb-2">@lang('communicate.role') <span class="text-danger">*</span></label>
                                        <select multiple id="selectMultiUsers" class="multypol_check_select active position-relative commonInputBlank" name="role_ids[]" style="width:300px">
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{@$editData ? (in_array($role->id, json_decode($editData->role_ids))? 'selected' : '') : '' }}>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="error_role" class="text-danger commonErrorBlank"></span>
                                        </div>

                                        <div class="col-lg-4">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('communicate.event_location') <span class="text-danger"> *</span> </label>
                                            <input class="primary_input_field form-control{{ $errors->has('event_location') ? ' is-invalid' : '' }} commonInputBlank"
                                            type="text" name="event_location" autocomplete="off" value="{{isset($editData)? $editData->event_location : old('event_location') }}">
                                            <span id="error_event_location" class="text-danger commonErrorBlank"></span>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-25">
                                    <div class="row">
                                        <div class="col-lg-6">
                                        <div class="primary_input">
                                            <label class="primary_input_label" for="">@lang('common.description') <span class="text-danger"> *</span> </label>
                                            <textarea class="primary_input_field form-control {{ $errors->has('event_des') ? ' is-invalid' : '' }} commonInputBlank" id="event_desData" cols="0" rows="4" name="event_des"></textarea>
                                            <span id="error_description" class="text-danger commonErrorBlank"></span>
                                        </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="primary_input">
                                                <label class="primary_input_label" for="">@lang('common.url')</label>
                                                <textarea class="primary_input_field form-control {{ $errors->has('url') ? ' is-invalid' : '' }} commonInputBlank" id="event_urlData" cols="0" rows="4" name="url"></textarea>
                                                <span id="error_url" class="text-danger commonErrorBlank"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center mt-25">
                                    <div class="d-flex justify-content-center">
                                        <button class="primary-btn fix-gr-bg submit" id="saveButtonForAddEvent" type="submit">@lang('admin.save')</button>
                                    </div>
                                </div>
                            </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>

    </div>
</section>
@include('backEnd.partials.multi_select_js')
@endsection
@push('script')
    <script src="{{asset('public/backEnd/')}}/full_calendar/js/index.global.min.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
          var calendarEl = document.getElementById('academicCalendar');
          var eventsData = @json($events);
          var system_url = $('#system_url').val();
      
          var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
              left: 'prevYear,prev,next,nextYear today',
              center: 'title',
              right: 'dayGridMonth,dayGridWeek,dayGridDay,listMonth'
            },
            initialDate: '{{\Carbon\Carbon::now()->format('Y-m-d')}}',
            navLinks: true, // can click day/week names to navigate views
            @if(userPermission('event-store'))
                editable: true,
                selectable: true,
            @endif
            dayMaxEvents: true, // allow "more" link when too many events
            events: eventsData,
            
            select: function(start, end, allDays){
              var startDate = start.start;
              var endDate = start.end;
                $('#currentDate').html('('+formatDate(start.start)+')');
                errorDataShow(true, null);
                $('#addEventOnCalendar').modal('show');
                $('#saveButtonForAddEvent').click(function(){
                    var event_title = $("input[name=event_title]").val();
                    var role_ids = $('#selectMultiUsers').val();
                    var event_location = $("input[name=event_location]").val();
                    var event_des = $('#event_desData').val();
                    var url = $('#event_urlData').val();
                    var from_date = moment(startDate).format('YYYY-MM-DD');
                    var to_date = moment(endDate).format('YYYY-MM-DD');
                    var data_type = 'ajax';
                    $.ajax({
                        url: '{{route("event")}}',
                        type: 'POST',
                        dataType: 'json',
                        data: {event_title, role_ids, event_location, event_des, url, from_date, to_date, data_type},
                        success: function(response){
                            $('#addEventOnCalendar').modal('hide');
                            location.reload();
                        },
                        error:function (xhr){
                            errorDataShow(null, xhr);
                        }
                    })
                })
            },
            eventClick:  function(event, jsEvent, view) {
                $('#downloadEventFile').addClass('d-none');
                $('#urlEventData').addClass('d-none');
                var startDate = formatDate(event.event.start);
                var endDate = formatDate(event.event.extendedProps.endDate);
                var startEndDate =  startDate + ' {{__("common.to")}} ' + endDate;
                $('#startAndEndDate').html(startEndDate);

                if(event.event.extendedProps.type == 'admission_query'){
                    $('#AQname').html(event.event.extendedProps.name);
                    $('#AQphone').html(event.event.extendedProps.phone);
                    $('#AQaddress').html(event.event.extendedProps.address);
                    $('#AQemail').html(event.event.extendedProps.email);
                    $('#AQdate').html(formatDate(event.event.start));
                    $('#admissionQueryUrl').attr('href', event.event.extendedProps.route);

                    $('.commonModalContent').addClass('d-none');
                    $('.admissionQueryModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'homework'){
                    $('#Hdescription').html(event.event.extendedProps.description);
                    $('#Hclass').html(event.event.extendedProps.class);
                    $('#Hsection').html(event.event.extendedProps.section);
                    $('#Hsubject').html(event.event.extendedProps.subject);
                    $('#Hdate').html(formatDate(event.event.start));
                    $('#homeworkRoute').attr('href', event.event.extendedProps.route);

                    $('.commonModalContent').addClass('d-none');
                    $('.homeworkModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'study_material'){
                    $('#SMtitle').html(event.event.extendedProps.content_title);
                    $('#SMtype').html(event.event.extendedProps.content_type);
                    $('#SMavailable').html(event.event.extendedProps.avaiable);
                    $('#SMdescription').html(event.event.extendedProps.description);
                    $('#SMdate').html(formatDate(event.event.start));

                    $('.commonModalContent').addClass('d-none');
                    $('.studyMaterialModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'event'){
                    $('#Etitle').html(event.event.extendedProps.content_title);
                    $('#Edescription').html(event.event.extendedProps.description);
                    $('#Elocation').html(event.event.extendedProps.location);
                    $('#Esdate').html(formatDate(event.event.start));
                    $('#Eedate').html(formatDate(event.event.extendedProps.endDate));
                    if(event.event.extendedProps.image){
                        $('#eventFile').removeClass('d-none');
                        $('#eventFile').attr('href', system_url+'/'+event.event.extendedProps.image);
                    }
                    if(event.event.extendedProps.link){
                        $('#eventLink').removeClass('d-none');
                        $('#eventLink').attr('href', event.event.extendedProps.link);
                    }

                    $('.commonModalContent').addClass('d-none');
                    $('.eventModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'holiday'){
                    $('#HDtitle').html(event.event.extendedProps.title_content);
                    $('#HDdescription').html(event.event.extendedProps.description);
                    $('#HDsdate').html(formatDate(event.event.start));
                    $('#HDedate').html(formatDate(event.event.extendedProps.endDate));
                    if(event.event.extendedProps.image){
                        $('#holidayFile').removeClass('d-none');
                        $('#holidayFile').attr('href', system_url+'/'+event.event.extendedProps.image);
                    }

                    $('.commonModalContent').addClass('d-none');
                    $('.holidayModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'exam'){
                    $('#EMname').html(event.event.extendedProps.exam_term);
                    $('#EMclass').html(event.event.extendedProps.class);
                    $('#EMsection').html(event.event.extendedProps.section);
                    $('#EMsubject').html(event.event.extendedProps.subject);
                    $('#EMteacher').html(event.event.extendedProps.teacher);
                    $('#EMroom').html(event.event.extendedProps.room);
                    $('#EMdate').html(event.event.extendedProps.endDate);
                    $('#EMstarttime').html(event.event.extendedProps.start_time);
                    $('#EMendtime').html(event.event.extendedProps.end_time);


                    $('.commonModalContent').addClass('d-none');
                    $('.examModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'notice_board'){
                    $('#NBtitle').html(event.event.extendedProps.title_content);
                    $('#NBdescription').html(event.event.extendedProps.notice_message);
                    $('#NBinform').html(event.event.extendedProps.inform_to);
                    $('#NBdate').html(formatDate(event.event.extendedProps.endDate));

                    $('.commonModalContent').addClass('d-none');
                    $('.noticeBoardModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'online_exam'){
                    $('#OEtitle').html(event.event.extendedProps.title_content);
                    $('#OEclass').html(event.event.extendedProps.class);
                    $('#OEsection').html(event.event.extendedProps.section);
                    $('#OEsubject').html(event.event.extendedProps.subject);
                    $('#OEstartdate').html(formatDate(event.event.start));
                    $('#OEenddate').html(formatDate(event.event.extendedProps.endDate));
                    $('#OEstarttime').html(event.event.extendedProps.start_time);
                    $('#OEendtime').html(event.event.extendedProps.end_time);

                    $('.commonModalContent').addClass('d-none');
                    $('.onlineExamModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'lesson_plan'){
                    $('#LPclass').html(event.event.extendedProps.class);
                    $('#LPsection').html(event.event.extendedProps.section);
                    $('#LPsubject').html(event.event.extendedProps.subject);
                    $('#LPteacher').html(event.event.extendedProps.teacher);
                    $('#LPdate').html(formatDate(event.event.start));

                    $('.commonModalContent').addClass('d-none');
                    $('.lessonPlanModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'leave'){
                    $('#Lname').html(event.event.extendedProps.name);
                    $('#Lreason').html(event.event.extendedProps.reason);
                    $('#Lstartdate').html(formatDate(event.event.start));
                    $('#Lenddate').html(formatDate(event.event.extendedProps.endDate));

                    $('.commonModalContent').addClass('d-none');
                    $('.leaveModal').removeClass('d-none');
                }else if(event.event.extendedProps.type == 'library'){
                    $('#Lbooktitle').html(event.event.extendedProps.book_name);
                    $('#Lduedate').html(formatDate(event.event.start));

                    $('.commonModalContent').addClass('d-none');
                    $('.libraryModal').removeClass('d-none');
                }
                $('#descriptionModal').modal('show');
            },
          });
          calendar.render();
        });

        function formatDate(date) {
          var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          day = '' + d.getDate(),
          year = d.getFullYear();
          var dayName = d.toLocaleString('en-us', {weekday: 'long'});
          if (month.length < 2) month = '0' + month;
          if (day.length < 2) day = '0' + day;
          return [dayName, day, month, year ].join('- ');
        }

        function errorDataShow(blank, xhr){
            if(blank){
                $('#error_event_title').html('');
                $('#error_role').html('');
                $('#error_event_location').html('');
                $('#error_description').html('');
                $('#error_url').html('');
            }else{
                $('#error_event_title').html(xhr.responseJSON.errors.event_title);
                $('#error_role').html(xhr.responseJSON.errors.role_ids);
                $('#error_event_location').html(xhr.responseJSON.errors.event_location);
                $('#error_description').html(xhr.responseJSON.errors.event_des);
                $('#error_url').html(xhr.responseJSON.errors.url);
            }
        }
    </script>
    <script>
        $( document ).ready(function() {
            $('.calenderSettingsJs').on('click', function(){
                $('.showAndHideSettings').slideToggle();
            })
        });
    </script>
@endpush
