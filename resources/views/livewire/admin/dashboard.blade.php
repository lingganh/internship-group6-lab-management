<div>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Event trong tuần</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{$AllEvent}}
                                    </h5>
                                </div>
                            </div>

                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">k.o biết</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        24
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Event chưa duyệt</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        {{$ALLPendingEvt}}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold">hỏng hóc</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        12
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-7 mb-lg-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="d-flex flex-column h-100">
                                    <p class="mb-1 pt-2 fs-3 fw-bold">
                                        Event chưa duyệt gần nhất
                                    </p>
                                    <h5 class="fs-2">
                                        {{$FirstEvent->title}}
                                    </h5>
                                    <p class="mb-5">
                                      {{$FirstEvent->start}}-{{$FirstEvent->end}}
                                    </p>
                                </div>
                            </div>


                            <div class="col-lg-5 ms-auto text-center mt-5 mt-lg-0">
                                <div class="bg-gradient-warning border-radius-lg h-100">

                                    <div class="position-relative d-flex align-items-center justify-content-center h-100">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card h-100 p-3">
                    <div class="overflow-hidden position-relative border-radius-lg bg-cover h-100" style="background-image: url('../assets/img/ivancik.jpg');">
                        <span class="mask bg-gradient-dark"></span>
                        <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">

                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="row mt-4">
            <div class="col-lg-5 mb-lg-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0 d-flex align-items-center">
                        <h6>insert graph</h6>

                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Chart range">
                            <button type="button"
                                class="btn btn-outline-primary active"
                                data-range="week">
                                Week
                            </button>
                            <button type="button"
                                class="btn btn-outline-primary"
                                data-range="month">
                                Month
                            </button>
                            <button type="button"
                                class="btn btn-outline-primary"
                                data-range="year">
                                Year
                            </button>
                        </div>
                    </div>
                    <div wire:ignore class="card-body p-3">
                        <canvas id="pieChart" class="chart-canvas" height="300px"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header pb-0 d-flex align-items-center">
                        <h6>insert graph</h6>

                        <div class="btn-group btn-group-sm  ms-auto" role="group" aria-label="Chart range">
                            <button type="button"
                                class="btn btn-outline-primary active"
                                data-range="week">
                                Week
                            </button>
                            <button type="button"
                                class="btn btn-outline-primary"
                                data-range="month">
                                Month
                            </button>
                            <button type="button"
                                class="btn btn-outline-primary"
                                data-range="year">
                                Year
                            </button>
                        </div>



                    </div>
                    <div wire:ignore class="card-body p-3">
                        <div class="chart">
                            <canvas id="barChart" class="chart-canvas" height="200px"></canvas>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

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