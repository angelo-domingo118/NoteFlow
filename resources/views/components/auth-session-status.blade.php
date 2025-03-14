@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'p-4 mb-4 rounded-lg bg-white/10 dark:bg-gray-800/30 backdrop-blur-lg border border-gray-200/20 font-medium text-sm text-green-600 dark:text-green-400']) }}>
        {{ $status }}
    </div>
@endif
