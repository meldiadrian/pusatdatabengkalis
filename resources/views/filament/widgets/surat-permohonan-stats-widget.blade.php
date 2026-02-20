<div class="fi-card rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">Total Surat Permohonan</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $this->getTotal() }}</p>
            <p class="mt-1 text-xs text-gray-500">Jumlah seluruh surat permohonan</p>
        </div>
        <div class="rounded-full bg-green-100 p-3">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
    </div>
</div>