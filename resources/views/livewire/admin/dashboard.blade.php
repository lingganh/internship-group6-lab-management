<div>
    <div class="dashboard-shell">
        <div class="dashboard-content">

            <!-- GREETING -->
            <div class="greeting">
                @php
                $hour = now()->hour;

                if ($hour >= 5 && $hour < 12) {
                    $greeting='Chào buổi sáng' ;
                    $icon='☀️' ;
                    $message='Chúc bạn có một ngày làm việc hiệu quả' ;
                    } elseif ($hour>= 12 && $hour < 18) {
                        $greeting='Chào buổi chiều' ;
                        $icon='🌤️' ;
                        $message='Chúc bạn làm việc năng suất' ;
                        } else {
                        $greeting='Chào buổi tối' ;
                        $icon='🌙' ;
                        $message='Chúc bạn một buổi tối thư giãn' ;
                        }
                        @endphp

                        <div class="greeting-left">

                        <div>
                            <h4>{{ $greeting }}</h4>
                            <p>{{ $message }}</p>

                        </div>



            </div>
            <div>
                <div class="greeting-icon">{{ $icon }}</div>
                <div class="greeting-date">
                    {{ now()->format('d/m/Y') }}
                </div>
            </div>


        </div>

        <!-- KPI -->
        <div class="kpi-grid">

            <div class="kpi-card">
                <div class="kpi-icon bg-primary"><i class="fa fa-calendar"></i></div>
                <div>
                    <small>Sự kiện 7 ngày</small>
                    <h3>{{ $AllEvent }}</h3>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon bg-warning"><i class="fa fa-clock"></i></div>
                <div>
                    <small>chờ duyệt</small>
                    <h3>{{ $ALLPendingEvt }}</h3>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon bg-danger"><i class="fa fa-triangle-exclamation"></i></div>
                <div>
                    <small>Thiết bị hỏng</small>
                    <h3>{{ $FaultyEquip }}</h3>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-icon bg-success"><i class="fa fa-screwdriver"></i></div>
                <div>
                    <small>Thiết bị đang sửa</small>
                    <h3>{{ $MaintaceEquip }}</h3>
                </div>

            </div>
        </div>

        <!-- CHARTS -->
        <div class="dashboard-grid">

            <div class="panel">
                <div class="panel-header d-flex justify-content-between align-items-center mb-2">
                    <h6>Sự kiện theo loại</h6>
                    <div wire:ignore class="btn-group">
                        <button id="PiebtnWeek" class="btn btn-sm btn-primary">Tuần</button>
                        <button id="PiebtnMonth" class="btn btn-sm btn-outline-primary">Tháng</button>
                        <button id="PiebtnAll" class="btn btn-sm btn-outline-primary">Tất cả</button>
                    </div>
                </div>
                <div class="chart-wrapper" wire:ignore>
                    <canvas id="pieChart" class="chart-canvas"></canvas>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header d-flex justify-content-between align-items-center mb-2">
                    <h6>Số lượng sự kiện</h6>
                    <div wire:ignore class="btn-group ">
                        <button id="BarbtnWeek" class="btn btn-sm btn-primary">Tuần</button>
                        <button id="BarbtnMonth" class="btn btn-sm btn-outline-primary">Tháng</button>
                    </div>
                </div>
                <div class="chart-wrapper" wire:ignore>
                    <canvas id="barChart" class="chart-canvas"></canvas>
                </div>
            </div>

        </div>

        <!-- EQUIPMENT + EVENTS -->
        <div class="dashboard-grid">

            <div class="panel">
                <h6 class="mb-3">Trạng thái thiết bị</h6>
                <div class="chart-warpper" wire:ignore>
                    <canvas id="equipChart" class="chart-canvas"></canvas>
                </div>
            </div>
            <div class="panel">
                <h6 class="mb-3">Sự kiện sắp tới</h6>

                <div class="event-list">
                    @forelse($TopEvent as $event)
                    <div class="event-item">
                        <div class="event-left">
                            <div class="event-title">{{ $event->title }}</div>
                            <div class="event-lab">{{ $event->lab_code }}</div>
                        </div>

                        <div class="event-time">
                            <span class="event-date">{{ $event->start->format('d/m') }}</span>
                            <span class="event-hour">{{ $event->start->format('H:i') }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        Không có sự kiện
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>


</div>



<!-- {{-- Styles --}} -->
<style>
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

    .dashboard-shell {
        padding: 28px 36px;
        background: #f4f7fb;
        min-height: 100vh;
    }

    .dashboard-content {
        max-width: 1450px;
        margin: 0 auto;
    }

    .greeting {
        background: linear-gradient(135deg, #eef4ff, #e6fbff);
        border-radius: 20px;
        padding: 26px 30px;
        display: flex;
        justify-content: space-between;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
    }

    .greeting-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        background: #fde68a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .greeting h4 {
        font-size: 1.1rem;
        margin-bottom: 4px;
    }

    .greeting p {
        font-size: 0.9rem;
        margin: 0;
        color: #475569;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin: 28px 0;
    }

    @media(max-width:1200px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .kpi-card {
        background: white;
        padding: 20px;
        border-radius: 18px;
        display: flex;
        gap: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    @media (max-width: 1200px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    .panel {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, .06);
    }

    .panel.wide {
        grid-column: span 1;
    }


    .chart-wrapper {
        position: relative;
        width: 100%;
        min-width: 0;
        /* 🔥 VERY IMPORTANT */
    }

    canvas {
        width: 100% !important;
        height: auto !important;
    }

    .btn:focus,
    .btn:active,
    .btn:focus-visible {
        box-shadow: none !important;
    }

    .event-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .event-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 18px;
        background: #f9fbff;
        border-radius: 14px;
        transition: all 0.2s ease;
    }

    .event-item:hover {
        background: #eef4ff;
        transform: translateY(-2px);
    }

    .event-left {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .event-title {
        font-weight: 600;
        color: #1f2937;
    }

    .event-lab {
        font-size: 13px;
        color: #6b7280;
    }

    .event-time {
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .event-date {
        font-weight: 600;
        color: #2563eb;
    }

    .event-hour {
        font-size: 13px;
        color: #6b7280;
    }
</style>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@script
<script>
    let barChart = null,
        pieChart = null;

    function makeGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    $wire.on('create_chart', () => {

        // 🔥 BAR CHART
        const barCanvas = document.getElementById('barChart');
        if (!barCanvas) return;

        const barCtx = barCanvas.getContext('2d');

        if (barChart) {
            barChart.destroy();
            barChart = null;
        }

        barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Sự kiện',
                    data: [],
                    backgroundColor: makeGradient(barCtx, '#60a5fa', '#2563eb'),
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: true, // 🔥 IMPORTANT
                resizeDelay: 300,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // 🔥 PIE CHART
        const pieCanvas = document.getElementById('pieChart');
        if (!pieCanvas) return;

        const pieCtx = pieCanvas.getContext('2d');

        if (pieChart) {
            pieChart.destroy();
            pieChart = null;
        }

        pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        makeGradient(pieCtx, '#60a5fa', '#2563eb'), // blue
                        makeGradient(pieCtx, '#34d399', '#059669'), // green
                        makeGradient(pieCtx, '#fbbf24', '#d97706'), // amber
                        makeGradient(pieCtx, '#f87171', '#dc2626'), // red
                        makeGradient(pieCtx, '#c084fc', '#7c3aed'), // purple
                        makeGradient(pieCtx, '#fb7185', '#be123c'), // pink
                        makeGradient(pieCtx, '#22d3ee', '#0891b2'), // cyan
                        makeGradient(pieCtx, '#a3e635', '#4d7c0f'), // lime
                    ],
                    borderWidth: 2,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: true, // 🔥 IMPORTANT
                resizeDelay: 300,
            }
        });

        bindChartResize(barChart);
        bindChartResize(pieChart);
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
        updatePieChart(data);

    });

    let equipChart = null;

    function translate(status) {
        if (status == 'Broken')
            return 'Bị Hỏng'
        else if (status == 'Maintenance')
            return 'Đang sửa chữa'
        else if (status == 'Available')
            return 'Có thể sử dụng'
        else if (status == 'In_use')
            return 'Đang trong sử dụng'
        else
            return status

    }

    $wire.on('push_data_equip', ({
        data
    }) => {

        const dat = data.map(d => d.count);
        const label = data.map(d => translate(d.status));

        const equipCanvas = document.getElementById('equipChart');
        const equipCtx = equipCanvas.getContext('2d');

        if (equipChart) {
            equipChart.destroy();
        }

        equipChart = new Chart(equipCtx, {
            type: 'pie',
            data: {
                labels: label,
                datasets: [{
                    data: dat,
                    backgroundColor: [
                        makeGradient(equipCtx, '#fb7185', '#be123c'),
                        makeGradient(equipCtx, '#60a5fa', '#2563eb'),
                        makeGradient(equipCtx, '#facc15', '#ca8a04'),
                    ],
                    borderWidth: 2,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: true, // 🔥 IMPORTANT
                resizeDelay: 300,

                plugins: {
                    title: {
                        display: true,
                        text: 'Trạng thái thiết bị',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }


            }
        });
    });

    function updateBarChart(Newdata) {
        if (!barChart) return;

        barChart.data.labels = Newdata.map(d => d.date);
        barChart.data.datasets[0].data = Newdata.map(d => d.count);


        barChart.update({
            duration: 600,
            easing: 'easeOutCubic'
        });


        console.log(Newdata)
    }


    function updatePieChart(Newdata) {
        if (!pieChart) return; // 🔥 guard

        pieChart.data.labels = Newdata.map(d => d.category);
        pieChart.data.datasets[0].data = Newdata.map(d => d.count);

        pieChart.update({
            duration: 600,
            easing: 'easeOutCubic'
        });


        console.log(Newdata)
    }

    /* ======================
       Buttons
    ====================== */

    // Pie buttons
    document.getElementById('PiebtnWeek').addEventListener('click', () => {
        $wire.loadPieWeek();
        setActiveButton('PiebtnWeek', ['PiebtnWeek', 'PiebtnMonth', 'PiebtnAll']);
    });

    document.getElementById('PiebtnMonth').addEventListener('click', () => {
        $wire.loadPieMonth();
        setActiveButton('PiebtnMonth', ['PiebtnWeek', 'PiebtnMonth', 'PiebtnAll']);
    });

    document.getElementById('PiebtnAll').addEventListener('click', () => {
        $wire.loadPieAll();
        setActiveButton('PiebtnAll', ['PiebtnWeek', 'PiebtnMonth', 'PiebtnAll']);
    });

    // Bar buttons
    document.getElementById('BarbtnWeek').addEventListener('click', () => {
        $wire.loadBarWeek();
        setActiveButton('BarbtnWeek', ['BarbtnWeek', 'BarbtnMonth']);
    });

    document.getElementById('BarbtnMonth').addEventListener('click', () => {
        $wire.loadBarMonth();
        setActiveButton('BarbtnMonth', ['BarbtnWeek', 'BarbtnMonth']);
    });

    // Active button helper
    function setActiveButton(activeId, allIds) {
        allIds.forEach(id => {
            const btn = document.getElementById(id);

            if (id === activeId) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            }


            btn.blur();
        });
    }

    function hardResetChart(chart) {
        if (!chart) return;

        const canvas = chart.canvas;
        const parent = canvas.parentElement;
        if (!parent) return;

        const rect = parent.getBoundingClientRect();
        const width = Math.floor(rect.width);
        const height = Math.floor(rect.height);

        console.log('[FORCE]', width, height);

        // 🔥 force canvas size
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';

        canvas.width = width;
        canvas.height = height;

        chart.resize();
    }


    function bindChartResize(chart) {
        const parent = chart.canvas.parentElement;

        const ro = new ResizeObserver(entries => {
            const {
                width,
                height
            } = entries[0].contentRect;
            console.log('[RO]', Math.round(width), Math.round(height));

            chart.resize();
        });

        ro.observe(parent);
    }
</script>
@endscript
</div>