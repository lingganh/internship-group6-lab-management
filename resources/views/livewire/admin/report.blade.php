<div class="dashboard-wrapper bg-light-subtle py-4">

    <!-- PAGE HEADER -->
    <div class="container-fluid px-4 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="fw-bold mb-1">Báo cáo Phòng Lab</h3>
                <p class="text-muted mb-0">
                    Theo dõi tình trạng sử dụng phòng và thiết bị
                </p>
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="container-fluid px-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="row g-3 align-items-end">

                <div class="col-lg-3 col-md-6">
                    <label class="filter-label">Phòng Lab</label>
                    <select class="form-select filter-input" wire:model.live="selectedLab">
                        <option value="all">Tất cả</option>
                        @foreach($LabList as $lab)
                        <option value="{{ $lab->id }}">
                            {{ $lab->name }} ({{ $lab->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="filter-label">Từ ngày</label>
                    <input type="date" class="form-control filter-input" wire:model.live="fromDate">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="filter-label">Đến ngày</label>
                    <input type="date" class="form-control filter-input" wire:model.live="toDate">
                </div>

                <div class="col-lg-3 col-md-12">
                    <button class="btn btn-primary w-100 h-52 rounded-3 fw-semibold"
                        wire:click="exportPdf"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>📄 Xuất PDF</span>
                        <span wire:loading>Đang xuất...</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- EVENTS TABLE -->
    <div class="container-fluid px-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white fw-semibold py-3">
                Lịch sử sử dụng phòng
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nội dung</th>
                            <th>Phòng</th>
                            <th>Người đăng ký</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $event->title }}</div>
                                <small class="text-muted">
                                    #{{ $event->id }} · {{ ucfirst($event->category) }}
                                </small>
                            </td>
                            <td>
                                {{ $event->lab_code }}
                            </td>
                            <td>
                                {{ $event->register_for ?? $event->user->full_name ?? 'System' }}
                            </td>
                            <td>
                                {{ $event->start->format('d/m/Y H:i') }} – {{ $event->end->format('H:i') }}
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $event->status=='approved'?'success':
                                    ($event->status=='pending'?'warning':'secondary')
                                }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Không có dữ liệu
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div wire:ignore class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <h6 class="fw-semibold mb-3">Thiết bị theo trạng thái</h6>
                    <div class="chart-box">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>
            </div>
            <div wire:ignore class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <h6 class="fw-semibold mb-3">Thiết bị theo loại</h6>
                    <div class="chart-box">
                        <canvas id="chart2"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .filter-bar {
            border: 1px solid #e9ecef;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #111827;
        }

        .filter-input {
            height: 52px;
            border-radius: 14px;
            font-size: 15px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
        }

        .filter-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, .15);
        }

        .filter-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            pointer-events: none;
        }

        .export-btn {
            height: 52px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
        }

        .dashboard-wrapper {
            max-width: 1600px;
            margin: 0 auto;
        }

        .h-52 {
            height: 52px;
        }

        .bg-light-subtle {
            background: #f8fafc;
        }

        .filter-input {
            height: 52px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
        }

        .card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .bg-light-subtle {
            background: #f8fafc;
        }

        .filter-input {
            height: 52px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
        }

        .card {
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .chart-box {
            height: 320px;
            position: relative;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    @script
    <script>
        let piechart1 = null;
        let piechart2 = null;

        function translate(status) {
            if (status == 'broken')
                return 'Bị Hỏng'
            else if (status == 'maintenance')
                return 'Đang sửa chữa'
            else if (status == 'available')
                return 'Có thể sử dụng'
            else if (status == 'In_use')
                return 'Đang trong sử dụng'
            else
                return status

        }


        $wire.on('create_chart', () => {
            const pieCtx1 = document.getElementById('chart1');
            piechart1 = new Chart(pieCtx1, {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [ // Array of colors for each slice
                            'rgb(255, 99, 132)', // Red
                            'rgb(54, 162, 235)', // Blue
                            'rgb(255, 205, 86)', // Yellow
                            'rgb(34, 197, 94)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Số thiết bị theo trạng thái',
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
            })

            const pieCtx2 = document.getElementById('chart2');
            piechart2 = new Chart(pieCtx2, {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            'rgb(34, 197, 94)', // Green 
                            'rgb(168, 85, 247)', // Purple
                            'rgb(251, 146, 60)', // Orange
                            'rgb(20, 184, 166)', // Teal
                            'rgb(244, 114, 182)', // Pink
                            'rgb(255, 99, 132)', // Red
                            'rgb(54, 162, 235)', // Blue
                            'rgb(255, 205, 86)', // Yellow 
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Số Thiết bị theo loại',
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

        $wire.on('push_PCData1', ({
            data
        }) => {
            const labelColors = {
                'Bị Hỏng': 'rgb(255, 99, 132)',
                'Có thể sử dụng': 'rgb(54, 162, 235)',
                'Đang trong sử dụng': '#10B981',
                'Đang sửa chữa': 'rgb(255, 205, 86)'
            };
          
            piechart1.data.labels = data.map(i => translate(i.status));
            piechart1.data.datasets[0].data = data.map(i => i.count)
            piechart1.data.datasets[0].backgroundColor =
                piechart1.data.labels.map(
                    l => labelColors[l] ?? '#9CA3AF'
                );
            piechart1.update();
        })

        $wire.on('push_PCData2', ({
            data
        }) => {
            piechart2.data.labels = data.map(i => i.type);
            piechart2.data.datasets[0].data = data.map(i => i.count)
            piechart2.update();
        })
    </script>
    @endscript

</div>