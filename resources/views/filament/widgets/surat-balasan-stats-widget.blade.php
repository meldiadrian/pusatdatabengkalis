<!-- <div class="fi-card rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">Total Surat Balasan</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $this->getTotal() }}</p>
            <p class="mt-1 text-xs text-gray-500">Jumlah seluruh surat balasan</p>
        </div>
        <div class="rounded-full bg-yellow-100 p-3">
            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
        </div>
    </div>
</div> -->

<div
    class="fi-card rounded-lg border p-6 shadow-sm text-white"
    style="
        box-shadow: 0 -4px 6px -2px rgba(0, 0, 255, 0.6);
        border-radius: 12px;
    ">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">Total Surat Balasan</p>
            <p class="mt-2 text-3xl font-bold text-gray-700">{{ $this->getTotal() }}</p>
            <p class="mt-1 text-xs text-gray-500">Jumlah seluruh surat balasan</p>
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