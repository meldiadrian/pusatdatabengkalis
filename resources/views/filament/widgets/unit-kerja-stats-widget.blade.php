<!-- <div class="fi-card rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">Total Unit Kerja</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $this->getTotal() }}</p>
            <p class="mt-1 text-xs text-gray-500">Jumlah seluruh unit kerja</p>
        </div>
        <div class="rounded-full bg-blue-100 p-3">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
    </div>
</div> -->

<div
    class="fi-card rounded-lg border p-6 shadow-sm text-white"
    style="
        box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4);
        border-radius: 12px;
    ">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">Total Unit Kerja</p>
            <p class="mt-2 text-3xl font-bold text-gray-700">{{ $this->getTotal() }}</p>
            <p class="mt-1 text-xs text-gray-500">Jumlah seluruh surat unit kerja</p>
        </div>

        <div class="rounded-full bg-white/20 p-3">
            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                </path>
            </svg>
        </div>
    </div>
</div>