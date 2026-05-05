<div style="text-align:center; margin-top:20px;">
    <img id="qrImage" style="display:none; width:200px; height:200px;" />
    <p id="qrText"></p>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        initQr();
    });

    async function initQr() {
        let success = await loadQr();

        // jika gagal / tidak ada qr → logout dulu
        if (!success) {
            console.log('QR tidak ada, logout dulu...');

            await logoutSession();

            // coba start ulang
            await loadQr();
        }
    }

    async function loadQr() {
        try {
            const response = await fetch("{{config('services.whatsapp.url')}}/session/start", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session: "{{config('services.whatsapp.token')}}"
                })
            });

            const data = await response.json();

            if (!data.qr) {
                return false; // trigger logout
            }

            const img = document.getElementById('qrImage');
            const text = document.getElementById('qrText');

            text.innerText = data.qr;

            img.src = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" +
                encodeURIComponent(data.qr);

            img.style.display = 'block';

            return true;

        } catch (error) {
            console.error('Gagal ambil QR:', error);
            return false;
        }
    }

    async function logoutSession() {
        try {
            await fetch("{{config('services.whatsapp.url')}}/session/logout", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session: "{{config('services.whatsapp.token')}}"
                })
            });

            console.log('Session berhasil di-logout');

        } catch (error) {
            console.error('Gagal logout:', error);
        }
    }
</script>