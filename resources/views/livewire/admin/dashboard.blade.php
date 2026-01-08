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
        <h6 class="fw-semibold mb-1">Tổng quan</h6>
        <small class="text-primary d-block mb-3">
            (Dữ liệu đợt mới nhất)
        </small>

        <div class="row g-3">
            <!-- KPI ITEM -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <small class="text-uppercase text-muted fw-semibold">
                                TỶ LỆ PHẢN HỒI
                            </small>
                            <h4 class="fw-bold mb-0">%</h4>
                            <small class="text-muted">Sinh viên</small>
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
                                CÓ VIỆC / PHẢN HỒI
                            </small>
                            <h4 class="fw-bold mb-0">%</h4>
                            <small class="text-muted">SV đã trả lời</small>
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
                                CÓ VIỆC / TỐT NGHIỆP
                            </small>
                            <h4 class="fw-bold mb-0">%</h4>
                            <small class="text-muted">Tổng SV</small>
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
                                VIỆC LÀM PHÙ HỢP
                            </small>
                            <h4 class="fw-bold mb-0">%</h4>
                            <small class="text-muted">Đúng ngành</small>
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

    <div class="text-center mb-4">
        <select class="form-select w-auto d-inline-block">
            <option>Tỉ lệ có việc làm / chưa có việc</option>
            <option>Chi tiết tình hình việc làm</option>
            <option>Khu vực làm việc</option>
        </select>
    </div>

    <!-- <div class="text-center my-5">
        <div class="spinner-border text-primary"></div>
        <p class="text-muted mt-2">Đang tải dữ liệu biểu đồ...</p>
    </div> -->

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm h-100">
                <div wire:ignore class="card-body p3" height="300px">
                    <h6 class="fw-bold text-center">Biểu đồ tròn</h6>
                    <canvas id="pieChart" class="chart-canvas" ></canvas>

                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body p-3" height="300px">
                    <h6 class="fw-bold text-center">Biểu đồ cột</h6>
                    <canvas id="barChart" class="chart-canvas" ></canvas>
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
    $wire.on('push_data_weekbc', ({
        data
    }) => {
        console.log(data);

        const ctx = document.getElementById('barChart');

        const labels = data.map(d => d.date);
        const counts = data.map(d => d.count);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '# of events',
                    data: counts,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });

    $wire.on('push_data_weekpc', ({
        data
    }) => {
        console.log(data);

        const ctx = document.getElementById('pieChart');

        const labels = data.map(d => d.category);
        const counts = data.map(d => d.count);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: '# of events',
                    data: counts,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

@endscript





