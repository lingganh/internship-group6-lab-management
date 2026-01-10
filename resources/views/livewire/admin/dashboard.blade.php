<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="alert alert-info d-flex align-items-center gap-3 mb-0" role="alert">
            <i class="fa-solid fa-sun fs-3 text-warning"></i>
            <div>
                <h5 class="mb-1 fw-bold">Chào buổi sáng</h5>
                <small class="text-muted">Chúc bạn có một ngày làm việc hiệu quả</small>
            </div>

        </div>
    </div>
</div>


<!-- General statsitic -->

<div class="card-body">
    <div class="border-bottom pb-3 mb-4">
        <h4 class="fw-bold mb-1">Tổng quan</h4>
        <p class="text-muted fs-6 mb-0">
            Event chưa duyệt gần nhất :

            @if(!is_null($FirstEvent))
            <span class="fw-bold">{{ $FirstEvent->title }}</span>
            diễn ra vào
            <span class="fw-semibold">{{ $FirstEvent->start }} – {{ $FirstEvent->end }}</span>
            <
                </p>
            @else
            <span class="fw-bold">Không có event nào</span>
            @endif
        </p>

    </div>

    <div class="row g-3">
        <!-- KPI ITEM -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <small class="text-uppercase text-muted fw-semibold">
                            Tổng số event trong 7 ngày kế tiếp
                        </small>
                        <h4 class="fw-bold mb-0">{{$AllEvent}}</h4>
                        <small class="text-muted">Event</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                        style="width:44px;height:44px;">
                        <i class="fa-solid fa-file-circle-check text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- repeat for others -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <small class="text-uppercase text-muted fw-semibold">
                            TỔNG SỐ EVENT ĐANG CHỜ DUYÊT
                        </small>
                        <h4 class="fw-bold mb-0">{{$ALLPendingEvt}}</h4>
                        <small class="text-muted">Event</small>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                        style="width:44px;height:44px;">
                        <i class="fa-solid fa-briefcase text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <small class="text-uppercase text-muted fw-semibold">
                            SỐ THIẾT BỊ HỎNG HÓC
                        </small>
                        <h4 class="fw-bold mb-0">{{$FaultyEquip}}</h4>
                        <small class="text-muted">trong {{$EuqipNum}} thiết bị</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                        style="width:44px;height:44px;">
                        <i class="fa-solid fa-graduation-cap text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <small class="text-uppercase text-muted fw-semibold">
                            SỐ THIẾT BỊ ĐANG SỬA CHỮA
                        </small>
                        <h4 class="fw-bold mb-0">{{$MaintaceEquip}}</h4>
                        <small class="text-muted">trong {{$EuqipNum}} thiết bị</small>
                    </div>
                    <div class="bg-purple bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                        style="width:44px;height:44px;">
                        <i class="fa-solid fa-star text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>



<!--Chart -->
<div class="container my-4">
    <h4 class="text-center fw-bold mb-3">
        Thống kê việc làm sinh viên theo đợt tốt nghiệp
    </h4>
    <div class="d-flex align-items-center mb-1 ">
        <h4 class="text-center fw-bold mb-3">
            Thống kê
        </h4>


    </div>

    <!-- <div class="text-center my-5">
        <div class="spinner-border text-primary"></div>
        <p class="text-muted mt-2">Đang tải dữ liệu biểu đồ...</p>
    </div> -->

    <div class="row g-4">

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100">
                <div wire:ignore class="card-body p3">
                    <div class="d-flex justify-content-end mb-2">
                        <div class="btn-group" role="group">
                            <button id="PiebtnWeek" type="button" class="btn btn-primary active px-4 py-2">Tuần</button>
                            <button id="PiebtnMonth" type="button" class="btn btn-outline-primary px-4 py-2">Tháng</button>
                            <button id="PiebtnAll" type="button" class="btn btn-outline-primary px-4 py-2">Tất cả</button>
                        </div>
                    </div>
                    <canvas id="pieChart" class="chart-canvas"></canvas>

                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-end mb-2">
                        <div class="btn-group" role="group">
                            <button id="BarbtnWeek" type="button" class="btn btn-primary active px-4 py-2">Tuần</button>
                            <button id="BarbtnMonth" type="button" class="btn btn-outline-primary px-4 py-2">Tháng</button>
                        </div>
                    </div>
                    <canvas id="barChart" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>


</div>

<div class="container my-4">

    <!-- <div class="text-center my-5">
        <div class="spinner-border text-primary"></div>
        <p class="text-muted mt-2">Đang tải dữ liệu biểu đồ...</p>
    </div> -->

    <div class="row g-4">

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100">
                <div wire:ignore class="card-body p3">

                    <canvas id="equipChart" class="chart-canvas"></canvas>

                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm h-100 overflow-hidden">
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <th colspan="5" class="text-center table-dark">
                                Event sắp tới
                            </th>

                            <tbody>
                                @forelse($TopEvent as $event)
                                <tr>
                                    {{-- Nội dung --}}
                                    <td>
                                        <div class="fw-semibold">{{ $event->title }}</div>
                                        <div class="text-muted small">
                                            #{{ $event->id }} • {{ ucfirst($event->category) }}
                                        </div>
                                    </td>

                                    {{-- Phòng --}}
                                    <td>
                                        <span class="fw-semibold">{{ $event->lab_code }}</span>
                                    </td>

                                    {{-- Người đăng ký --}}
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $event->user?->name ?? 'System' }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $event->user?->email ?? '' }}
                                        </div>
                                    </td>

                                    {{-- Thời gian --}}
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $event->start->format('d/m/Y') }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $event->start->format('H:i') }}
                                            – {{ $event->end->format('H:i') }}
                                        </div>
                                    </td>


                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Không có event
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="text-end py-3">
                                        <a href="{{route('admin.lab-diary')}}" class="fw-semibold text-decoration-none">
                                            Xem thêm →
                                        </a>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>


</div>


<!-- {{-- Styles --}} -->
<style>
    :root {
        --ink: #0f172a;
        --muted: #667085;
        --line: #e7eaee;
        --bg: #ffffff;
        --brand: #2563eb;
        --brand-2: #06b6d4;
        --blue: #3b82f6;
        --green: #10b981;
        --purple: #8b5cf6;
        --amber: #f59e0b;
        --red: #ef4444;
        --radius: 14px;
        --shadow: 0 10px 30px rgba(2, 6, 23, .08);
    }

    .custom-greeting {
        background: linear-gradient(135deg, #eef2ff 0%, #ecfeff 100%);
        border: 1px solid #dbeafe;
    }

    .text-gradient {
        background: linear-gradient(90deg, var(--brand), var(--brand-2));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    #greetIcon {
        color: #f59e0b
    }

    .kpi-card {
        border: 1px solid var(--line)
    }

    .kpi-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 1.1rem
    }

    .kpi-blue {
        background: var(--blue)
    }

    .kpi-green {
        background: var(--green)
    }

    .kpi-purple {
        background: var(--purple)
    }

    .kpi-amber {
        background: var(--amber)
    }

    .charts-title {
        text-align: center;
        margin-bottom: 12px;
        color: var(--ink);
        font-weight: 800
    }

    .select {
        font-size: .95rem;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid var(--line);
        min-width: 280px;
        background: #fff;
        color: var(--ink)
    }

    .panel {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 14px;
        box-shadow: var(--shadow)
    }

    .panel-title {
        margin: 6px 6px 8px;
        color: #334155;
        font-weight: 800;
        font-size: 1rem
    }

    .chart-box {
        width: 100%;
        height: 440px;
        border-radius: 12px
    }

    @media (max-width:768px) {
        .chart-box {
            height: 360px
        }
    }

    #pieChart .am5-Legend {
        margin-top: 8px !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@script
<script>
    let barChart = null,
        pieChart = null;

    $wire.on('create_chart', () => {
        const barCtx = document.getElementById('barChart');
        barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: '# of events',
                    data: [],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Biểu đồ số lượng sự kiện', // <-- Your title here
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        });

        const pieCtx = document.getElementById('pieChart');
        pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [ // Array of colors for each slice
                        'rgb(255, 99, 132)', // Red
                        'rgb(54, 162, 235)', // Blue
                        'rgb(255, 205, 86)' // Yellow
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Tỉ lệ sự kiện theo loại', // <-- Your title here
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });




    });



    /* ======================
       Livewire events
    ====================== */

    // Bar chart data
    $wire.on('push_data_weekbc', ({
        data
    }) => {
        updateBarChart(data);
        // console.log(data)
    });
    $wire.on('push_data_monthbc', ({
        data
    }) => {
        updateBarChart(data);
        console.log(data)
    });

    // Pie chart data
    $wire.on('push_data_weekpc', ({
        data
    }) => {
        updatePieChart(data);
        // console.log(data)
    });
    $wire.on('push_data_monthpc', ({
        data
    }) => {
        updatePieChart(data);
        // console.log(data)    
    });
    $wire.on('push_data_allpc', ({
        data
    }) => {
        // updatePieChart(data);
        console.log(data)
    });

    $wire.on('push_data_equip', ({
        data
    }) => {
        console.log(data)
        dat = data.map(d => d.count)
        label = data.map(d => d.status)
        const equipCtx = document.getElementById('equipChart');
        new Chart(equipCtx, {
            type: 'pie',
            data: {
                labels: label,
                datasets: [{
                    data: dat,
                    backgroundColor: [ // Array of colors for each slice
                        'rgb(255, 99, 132)', // Red
                        'rgb(54, 162, 235)', // Blue
                        'rgb(255, 205, 86)' // Yellow
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Tỉ lệ dụng cụ theo trạng thái',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    });

    function updateBarChart(Newdata) {
        barChart.data.labels = Newdata.map(d => d.date);
        barChart.data.datasets[0].data = Newdata.map(d => d.count);
        barChart.update();

    }


    function updatePieChart(Newdata) {
        pieChart.data.labels = Newdata.map(d => d.category);
        pieChart.data.datasets[0].data = Newdata.map(d => d.count);
        pieChart.update();

    }


    /* ======================
       Buttons
    ====================== */

    // Pie buttons
    document.getElementById('PiebtnWeek').addEventListener('click', () => {
        $wire.loadPieWeek(); // Livewire method for weekly pie data
        toggleButtons('PiebtnWeek', ['PiebtnMonth', 'PiebtnAll']);
    });

    document.getElementById('PiebtnMonth').addEventListener('click', () => {
        $wire.loadPieMonth(); // Livewire method for monthly pie data
        toggleButtons('PiebtnMonth', ['PiebtnWeek', 'PiebtnAll']);
    });

    document.getElementById('PiebtnAll').addEventListener('click', () => {
        $wire.loadPieAll(); // Livewire method for all-time pie data
        toggleButtons('PiebtnAll', ['PiebtnWeek', 'PiebtnMonth']);
    });

    // Bar buttons
    document.getElementById('BarbtnWeek').addEventListener('click', () => {
        $wire.loadBarWeek();
        toggleButton('BarbtnWeek', 'BarbtnMonth');
    });

    document.getElementById('BarbtnMonth').addEventListener('click', () => {
        $wire.loadBarMonth();
        toggleButton('BarbtnMonth', 'BarbtnWeek');
    });

    // Active button helper
    function toggleButton(activeId, inactiveId) {
        document.getElementById(activeId).classList.add('btn-primary', 'active');
        document.getElementById(activeId).classList.remove('btn-outline-primary');

        document.getElementById(inactiveId).classList.add('btn-outline-primary');
        document.getElementById(inactiveId).classList.remove('btn-primary', 'active');
    }

    function toggleButtons(activeId, inactiveIds) {
        // Active button
        const activeBtn = document.getElementById(activeId);
        activeBtn.classList.add('btn-primary', 'active');
        activeBtn.classList.remove('btn-outline-primary');

        // Inactive buttons
        inactiveIds.forEach(id => {
            const btn = document.getElementById(id);
            btn.classList.add('btn-outline-primary');
            btn.classList.remove('btn-primary', 'active');
        });
    }
</script>
@endscript