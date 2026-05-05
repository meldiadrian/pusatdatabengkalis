<!-- LOADER -->

<div id="page-loader">
    <img src="/images/load.gif" alt="Loading" class="loader-gif">
</div>

<!-- STYLE -->

<style>
    #page-loader {
        position: fixed;
        inset: 0;
        background: white;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 1;
        visibility: visible;
        transition: opacity 0.4s ease, visibility 0.4s;
    }

    .loader-gif {
        width: 160px;
        max-width: 80%;
        height: auto;
    }

    /* efek sembunyi */
    #page-loader.hide {
        opacity: 0;
        visibility: hidden;
    }
</style>

<!-- SCRIPT -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('page-loader');

        setTimeout(() => {
            if (loader) {
                loader.style.opacity = '0';
                loader.style.transition = 'opacity 0.3s ease';
                setTimeout(() => loader.remove(), 300);
            }
        }, 1500);
    });
</script>

<script>
    document.addEventListener('livewire:navigated', () => {
        const loader = document.getElementById('page-loader');

        if (loader) {
            loader.style.display = 'flex';

            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => loader.remove(), 300);
            }, 1500);
        }
    });
</script>