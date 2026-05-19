<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span>🤖</span>
                <span>AI Weekly Insight</span>
                @if($this->getInsight()['generated_at'])
                    <span class="text-xs font-normal text-gray-400 ml-2">
                        Generated {{ \Carbon\Carbon::parse($this->getInsight()['generated_at'])->diffForHumans() }}
                    </span>
                @endif
            </div>
        </x-slot>

        @php $insight = $this->getInsight(); @endphp

        <div class="rounded-xl bg-gradient-to-br from-violet-50 to-indigo-50 dark:from-violet-900/20 dark:to-indigo-900/20 border border-violet-200 dark:border-violet-800 p-5">
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ $insight['text'] }}
            </p>

            @if(!empty($insight['stats']))
            <div class="mt-4 grid grid-cols-4 gap-3">
                @foreach([
                    ['New Requests', $insight['stats']['new_requests'] ?? 0],
                    ['Completed', $insight['stats']['completed'] ?? 0],
                    ['Providers', $insight['stats']['active_providers'] ?? 0],
                    ['Revenue', '₵' . number_format($insight['stats']['revenue_week'] ?? 0, 2)],
                ] as [$label, $value])
                <div class="bg-white dark:bg-gray-900/50 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-violet-600 dark:text-violet-400">{{ $value }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $label }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="mt-3 flex justify-end">
            <p class="text-xs text-gray-400">
                Powered by GPT-4o-mini &bull; Run <code class="bg-gray-100 dark:bg-gray-800 px-1 rounded">php artisan insights:generate</code> to refresh
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
