@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2>Scan QR Code</h2>
    <div id="qr-reader" style="width: 300px; margin: auto;"></div>
</div>

<script>
    const bearerToken = @json($token); // ← Inject token dari controller
</script>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Hentikan kamera
        html5QrcodeScanner.clear().then(() => {
            alert("QR Code berhasil dipindai:\n" + decodedText);

            fetch(decodedText, {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + bearerToken,
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (response.ok) {
                    alert("Absen berhasil!");
                    location.reload();
                } else {
                    alert("Gagal absen. Status kode: " + response.status);
                    location.reload();
                }
            })
            .catch(error => {
                alert("Terjadi kesalahan saat memproses QR code.");
                console.error(error);
            });
        }).catch(error => {
            console.error('Gagal membersihkan scanner.', error);
        });
    }

    function onScanFailure(error) {
        // Tidak perlu tampilkan error setiap frame
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", {
            fps: 10,
            qrbox: 250
        });

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endsection
