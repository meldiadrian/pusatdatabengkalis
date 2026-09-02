<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div wire:ignore>
        <div
            id="recaptcha-container"
            class="g-recaptcha"
            data-sitekey="{{ config('captcha.sitekey') }}"
            data-callback="onRecaptchaSuccess"
            data-expired-callback="onRecaptchaExpired"
            data-error-callback="onRecaptchaError"
            data-theme="light"
        ></div>
    </div>

    <script>
        /**
         * Cari tombol submit login Filament.
         * Filament 3 merender <button type="submit"> di dalam form.
         */
        function getLoginButton() {
            return document.querySelector('button[type="submit"]');
        }

        /**
         * Kunci tombol submit — tampilan abu-abu + cursor not-allowed.
         */
        function disableLoginButton() {
            const btn = getLoginButton();
            if (!btn) return;
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor  = 'not-allowed';
            btn.title = 'Silakan selesaikan verifikasi reCAPTCHA terlebih dahulu';
        }

        /**
         * Buka tombol submit — kembalikan ke kondisi normal.
         */
        function enableLoginButton() {
            const btn = getLoginButton();
            if (!btn) return;
            btn.disabled = false;
            btn.style.opacity = '';
            btn.style.cursor  = '';
            btn.title = '';
        }

        // Kunci tombol saat pertama kali halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            disableLoginButton();
        });

        // Juga kunci saat Livewire SPA navigasi (mode spa aktif)
        document.addEventListener('livewire:navigated', function () {
            disableLoginButton();

            // Reset widget jika sudah di-render
            if (typeof grecaptcha !== 'undefined') {
                try { grecaptcha.reset(); } catch (e) {}
            }
        });

        // ─── Callback dari Google reCAPTCHA ───────────────────────────

        // Berhasil centang → simpan token ke Livewire + buka tombol
        function onRecaptchaSuccess(token) {
            @this.set('{{ $getStatePath() }}', token);
            enableLoginButton();
        }

        // Token expired (setelah ~2 menit) → hapus token + kunci tombol
        function onRecaptchaExpired() {
            @this.set('{{ $getStatePath() }}', '');
            disableLoginButton();
        }

        // Error widget → hapus token + kunci tombol
        function onRecaptchaError() {
            @this.set('{{ $getStatePath() }}', '');
            disableLoginButton();
        }

        // ─── Listen event reset dari Login.php (saat login gagal) ─────
        Livewire.on('reset-recaptcha', () => {
            if (typeof grecaptcha !== 'undefined') {
                try { grecaptcha.reset(); } catch (e) {}
            }
            // Kunci tombol kembali setelah gagal login
            disableLoginButton();
        });
    </script>
</x-dynamic-component>
