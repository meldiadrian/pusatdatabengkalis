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
        // Callback saat user berhasil mencentang captcha
        function onRecaptchaSuccess(token) {
            @this.set('{{ $getStatePath() }}', token);
        }

        // Callback saat token captcha expired (biasanya 2 menit)
        function onRecaptchaExpired() {
            @this.set('{{ $getStatePath() }}', '');
        }

        // Callback saat terjadi error pada widget captcha
        function onRecaptchaError() {
            @this.set('{{ $getStatePath() }}', '');
        }

        // Reset captcha setelah Livewire navigasi (SPA mode) atau validation error
        document.addEventListener('livewire:navigated', () => {
            if (typeof grecaptcha !== 'undefined') {
                try {
                    grecaptcha.reset();
                } catch (e) {
                    // Widget belum di-render, abaikan
                }
            }
        });

        // Listen untuk custom event reset dari Login.php
        Livewire.on('reset-recaptcha', () => {
            if (typeof grecaptcha !== 'undefined') {
                try {
                    grecaptcha.reset();
                } catch (e) {
                    // Abaikan jika widget belum siap
                }
            }
        });
    </script>
</x-dynamic-component>
