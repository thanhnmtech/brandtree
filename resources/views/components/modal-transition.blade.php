@props(['type' => 'overlay'])

@php
// Định nghĩa transition configs nhất quán cho toàn bộ app
$transitions = [
    'overlay' => [
        'enter' => 'tw-transition tw-ease-out tw-duration-200',
        'enter-start' => 'tw-opacity-0',
        'enter-end' => 'tw-opacity-100',
        'leave' => 'tw-transition tw-ease-in tw-duration-150',
        'leave-start' => 'tw-opacity-100',
        'leave-end' => 'tw-opacity-0',
    ],
    'modal' => [
        'enter' => 'tw-transition tw-ease-out tw-duration-200 tw-transform',
        'enter-start' => 'tw-opacity-0 tw-scale-95',
        'enter-end' => 'tw-opacity-100 tw-scale-100',
        'leave' => 'tw-transition tw-ease-in tw-duration-150 tw-transform',
        'leave-start' => 'tw-opacity-100 tw-scale-100',
        'leave-end' => 'tw-opacity-0 tw-scale-95',
    ],
    'dropdown' => [
        'enter' => 'tw-transition tw-ease-out tw-duration-100',
        'enter-start' => 'tw-opacity-0 tw-scale-95',
        'enter-end' => 'tw-opacity-100 tw-scale-100',
        'leave' => 'tw-transition tw-ease-in tw-duration-75',
        'leave-start' => 'tw-opacity-100 tw-scale-100',
        'leave-end' => 'tw-opacity-0 tw-scale-95',
    ],
    'fade' => [
        'enter' => 'tw-transition tw-ease-out tw-duration-200',
        'enter-start' => 'tw-opacity-0',
        'enter-end' => 'tw-opacity-100',
        'leave' => 'tw-transition tw-ease-in tw-duration-150',
        'leave-start' => 'tw-opacity-100',
        'leave-end' => 'tw-opacity-0',
    ],
];

$config = $transitions[$type] ?? $transitions['modal'];
@endphp

<div {{ $attributes->merge([
    'x-transition:enter' => $config['enter'],
    'x-transition:enter-start' => $config['enter-start'],
    'x-transition:enter-end' => $config['enter-end'],
    'x-transition:leave' => $config['leave'],
    'x-transition:leave-start' => $config['leave-start'],
    'x-transition:leave-end' => $config['leave-end'],
]) }}>
    {{ $slot }}
</div>
