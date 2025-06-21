@extends('layouts.app')

@section('content')
<h3 class="mb-4">Statistik Event: {{ $event->nama }}</h3>

<div class="row">
    <div class="col-md-12 mb-4">
        <div id="donutChartContainer"></div>
    </div>
    <div class="col-md-6 mb-4">
        <div id="barChartContainer"></div>
    </div>
    <div class="col-md-6 mb-4">
        <div id="pieChartContainer"></div>
    </div>
    
</div>
@endsection

@push('scripts')
<!-- Highcharts CDN -->
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hadir = {{ $hadir }};
        const tidak = {{ $tidak }};
        const belum = {{ $belum }};
        const kuota = {{ $kuota }};
        const total = {{ $total }};
        const sisaKuota = kuota - total > 0 ? kuota - total : 0;

        // Bar Chart
        Highcharts.chart('barChartContainer', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Laporan Kehadiran Peserta'
            },
            xAxis: {
                categories: ['Hadir', 'Tidak Hadir', 'Belum Hadir']
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Peserta'
                }
            },
            series: [{
                name: 'Peserta',
                data: [hadir, tidak, belum],
                colorByPoint: true,
                colors: ['#28a745', '#dc3545', '#6c757d']
            }]
        });

        // Pie Chart - Distribusi Kehadiran
        Highcharts.chart('pieChartContainer', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Distribusi Kehadiran Peserta'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y} orang</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Peserta',
                colorByPoint: true,
                data: [
                    { name: 'Hadir', y: hadir, color: '#28a745' },
                    { name: 'Tidak Hadir', y: tidak, color: '#dc3545' },
                    { name: 'Belum Hadir', y: belum, color: '#6c757d' }
                ]
            }]
        });

        // Donut Chart - Kuota vs Terdaftar
        Highcharts.chart('donutChartContainer', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'Perbandingan Kuota dan Pendaftar'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y} orang</b>'
            },
            plotOptions: {
                pie: {
                    innerSize: '50%',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                data: [
                    { name: 'Terdaftar', y: total, color: '#007bff' },
                    { name: 'Sisa Kuota', y: sisaKuota, color: '#e0e0e0' }
                ]
            }]
        });
    });
</script>
@endpush
