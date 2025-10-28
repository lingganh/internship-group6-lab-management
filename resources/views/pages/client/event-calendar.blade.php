@php
use App\Common\Constants
@endphp


<x-client-layout>
    <div class="content seminar">

        <!-- Basic view -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0 text-uppercase">CÁC SỰ KIỆN SẮP DIỄN RA</h4>
            </div>

            <div class="card-body">
{{--                <x-seminar-section :events="$seminars" :new="true"></x-seminar-section>--}}
                <div class="seminar-section">
                    <div class="seminar-list">
{{--                        @forelse($events as $event)--}}
{{--                            <x-seminar-item :event="$event" :new="$new"/>--}}
                        <div class="seminar-item">
                            <div class="date_stamp">
                                <div class="date_stamp_top">
                                    <div class="weekday"> Thứ 6 </div>
                                    <div class="event_date"> 13</div>
                                </div>
                                <p class="date_time_bottom">10/2025</p>
                            </div>
                            <div class="seminar_item_right">
                                <div class="article-title"><a href="">Nghiên cứu khoa học</a> <span class="badge bg-danger">Sắp diễn ra</span>
                                </div>
                                <div class="seminar_info">
                                    <div><i>Bắt đầu:</i> 13:30</div>
                                    <div><i>Địa điểm:</i> {{ Constants::ROOM_NAME }}</div>
                                    <div><i>Nhóm:</i> NCKH1</div>
                                    <div><i>Người thuyết trình:</i> Nguyễn Văn A </div>
                                </div>
                            </div>
                        </div>
{{--                        @empty--}}
                            <div class="empty-calendar text-center">
                                <img src="{{asset('assets/images/empty-calendar.png') }}" alt="empty">
                                <div class="text-center mb-4">Không có sự kiện nào! </div>
                            </div>
{{--                        @endforelse--}}
                    </div>
                </div>
            </div>
        </div>
        <!-- /basic view -->

        <!-- Basic view -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0 text-uppercase">CÁC SỰ KIỆN ĐÃ DIỄN RA</h4>
                <form action="" class="filter-seminar" id="frm-filter-seminar">
                    <div class="form-group">
                        <select name="year" id="filter-select-year" class="form-select">
                            <option value="" @if(request()->query('year') == '') selected @endif>Tất cả</option>
                            @foreach(Constants::YEAR as $year)
                                <option value="{{ $year }}"
                                        @if(request()->query('year') == $year) selected @endif>Năm {{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="card-body">
                {{--                <x-seminar-section :events="$seminars" :new="true"></x-seminar-section>--}}
                <div class="seminar-section">
                    <div class="seminar-list">
                        {{--                        @forelse($events as $event)--}}
                        {{--                            <x-seminar-item :event="$event" :new="$new"/>--}}
                        <div class="seminar-item">
                            <div class="date_stamp">
                                <div class="date_stamp_top">
                                    <div class="weekday"> Thứ 6 </div>
                                    <div class="event_date"> 13</div>
                                </div>
                                <p class="date_time_bottom">10/2025</p>
                            </div>
                            <div class="seminar_item_right">
                                <div class="article-title"><a href="">Nghiên cứu khoa học</a>
                                </div>
                                <div class="seminar_info">
                                    <div><i>Bắt đầu:</i> 13:30</div>
                                    <div><i>Địa điểm:</i> {{ Constants::ROOM_NAME }}</div>
                                    <div><i>Nhóm:</i> NCKH1</div>
                                    <div><i>Người thuyết trình:</i> Nguyễn Văn A </div>
                                </div>
                            </div>
                        </div>
                        {{--                        @empty--}}
                        <div class="empty-calendar text-center">
                            <img src="{{asset('assets/images/empty-calendar.png') }}" alt="empty">
                            <div class="text-center mb-4">Không có sự kiện nào! </div>
                        </div>
                        {{--                        @endforelse--}}
                    </div>
                </div>
            </div>
        </div>
        <!-- /basic view -->
    </div>
</x-client-layout>
