@extends('layouts.authenticated')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Scanner Kehadiran</h1>
        <p class="text-sm text-gray-500 mt-1">Arahkan kamera ke QR Code yang ditampilkan guru Anda.</p>
    </div>
    <a href="{{ route('student.attendances.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors gap-2 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Riwayat Absensi
    </a>
</div>

<div class="max-w-md mx-auto">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        
        <!-- Video Stream Area -->
        <div class="relative bg-black w-full aspect-square flex items-center justify-center" id="reader-container">
             <!-- Scanner Placeholder/Loading -->
            <div id="scanner-loading" class="absolute inset-0 flex flex-col items-center justify-center text-white/50 z-10">
                <svg class="w-10 h-10 animate-spin mb-3 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <p class="text-sm font-medium">Mengaktifkan Kamera...</p>
            </div>

            <!-- Target Reader HTML5 QR Code -->
            <div id="reader" class="w-full h-full relative z-20"></div>

            <!-- Scanner Border Overlay -->
            <div class="absolute inset-0 pointer-events-none z-30">
                <div class="w-full h-full border-[40px] border-black/40 relative">
                    <div class="absolute w-12 h-12 border-l-4 border-t-4 border-brand-500 -top-1 -left-1"></div>
                    <div class="absolute w-12 h-12 border-r-4 border-t-4 border-brand-500 -top-1 -right-1"></div>
                    <div class="absolute w-12 h-12 border-l-4 border-b-4 border-brand-500 -bottom-1 -left-1"></div>
                    <div class="absolute w-12 h-12 border-r-4 border-b-4 border-brand-500 -bottom-1 -right-1"></div>
                </div>
            </div>
        </div>

        <!-- Scan Result Area -->
        <div class="p-6 text-center">
            <div id="scan-result-container" class="hidden">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white shadow-sm ring-1 ring-green-100">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-1">Berhasil Memindai</h3>
                <p class="text-sm text-gray-500 mb-4" id="scan-message">Memverifikasi kehadiran Anda...</p>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-2 overflow-hidden">
                    <div class="bg-brand-500 h-1.5 rounded-full w-2/3 animate-[pulse_1s_ease-in-out_infinite]"></div>
                </div>
            </div>

            <div id="scan-guide">
                <h3 class="font-bold text-gray-900 mb-1">Instruksi</h3>
                <p class="text-sm text-gray-500">Posisikan QR Code di dalam kotak sasaran terang. Pemindaian akan berlangsung secara otomatis.</p>
                
                @if($prefilledToken)
                    <div class="mt-4 p-3 bg-brand-50 border border-brand-100 rounded-xl">
                        <p class="text-xs text-brand-700 font-medium mb-2">Token terdeteksi dari URL</p>
                        <button onclick="processToken('{{ $prefilledToken }}')" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-lg transition-colors">
                            Pakai Token Ini
                        </button>
                    </div>
                @endif
            </div>

            <!-- Error Error Alert from AJAX -->
            <div id="scan-error" class="hidden mt-4 p-4 rounded-xl bg-red-50 border border-red-100 text-left flex gap-3">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <h4 class="text-sm font-bold text-red-800">Gagal Absen</h4>
                    <p class="text-xs text-red-600 mt-1" id="scan-error-message">Token QR salah atau kadaluarsa.</p>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Load Library Scanner QR berbasis WebRTC -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    let html5QrcodeScanner = null;
    let isProcessing = false;

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return; // Mencegah multi-submit jika scanner mendeteksi frame ganda secara instan
        processToken(decodedText);
    }

    function onScanFailure(error) {
        // Abaikan gagal parse (biasa terjadi berulang sebelum kamera fokus)
    }

    function processToken(token) {
        isProcessing = true;
        
        // Hentikan kamera (visual)
        if(html5QrcodeScanner) {
            html5QrcodeScanner.pause();
        }

        // Tampilkan State Loading
        document.getElementById('scan-guide').classList.add('hidden');
        document.getElementById('scan-error').classList.add('hidden');
        document.getElementById('scan-result-container').classList.remove('hidden');

        // Kirim AJAX Auth via Axios atau fetch murni (Kita pakai fetch murni Laravel)
        fetch("{{ route('student.attendance.process') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_token: token })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Sukses
                document.getElementById('scan-message').innerText = data.message;
                setTimeout(() => {
                    if(data.redirect) {
                        window.location.href = data.redirect;
                    }
                }, 2000);
            } else {
                throw new Error(data.message || 'Merespon error tidak diketahui.');
            }
        })
        .catch(error => {
            // Error dari network atau status 4xx
             console.error('Error:', error);
             document.getElementById('scan-result-container').classList.add('hidden');
             
             const errorBox = document.getElementById('scan-error');
             const errorMsg = document.getElementById('scan-error-message');
             
             errorBox.classList.remove('hidden');
             errorMsg.innerText = error.message.replace('Error: ', '');

             isProcessing = false;
             if(html5QrcodeScanner) {
                 html5QrcodeScanner.resume(); // Lanjutkan camera jika salah scan, biar bisa coba lagi
             }
        });
    }

    // Inisialisasi Kamera saat DOM ready
    document.addEventListener("DOMContentLoaded", function() {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { fps: 10, qrbox: {width: 250, height: 250}, aspectRatio: 1.0 }, 
            /* verbose= */ false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        
        // Sembunyikan loading jika container reader sudah ada konten
        const targetNode = document.getElementById('reader');
        const config = { childList: true, subtree: true };
        const callback = function(mutationsList, observer) {
            for(let mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    if (targetNode.innerHTML.includes('video')) {
                        document.getElementById('scanner-loading').style.display = 'none';
                        observer.disconnect();
                    }
                }
            }
        };
        const observer = new MutationObserver(callback);
        observer.observe(targetNode, config);
    });
</script>

<!-- Tambahan Stylesheet supaya custom player html5-qrcode lebih bagus menimpa default inline-styles-nya -->
<style>
    #reader { border: none !important; }
    #reader__dashboard_section_csr { margin-bottom: 20px; }
    #reader__dashboard_section_csr button {
        background-color: #2563eb; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; display: block; width: 100%; margin: 10px 0;
    }
    #reader video {
        object-fit: cover !important; width: 100% !important; height: 100% !important;
    }
</style>
@endsection
