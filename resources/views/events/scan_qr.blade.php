@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2>Scan QR Code</h2>
    <div id="qr-reader" style="width: 300px; margin: auto;"></div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Hentikan kamera
        html5QrcodeScanner.clear().then(() => {
            alert("QR Code berhasil dipindai!");
            // Redirect ke URL dari QR
            window.location.href = decodedText;
        }).catch(error => {
            console.error('Gagal membersihkan scanner.', error);
        });
    }

    function onScanFailure(error) {
        // console.warn(`QR scan failed: ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 });

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection
