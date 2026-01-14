@extends('layouts.app')

@section('title', 'Bản Đồ Hành Trình Gốc Cây')

@section('content')
<main class="tw-mt-[36px] tw-flex tw-flex-col tw-gap-10">

    @include('dashboard.partials.hero')

    @include('dashboard.partials.header')

    <div class="tw-px-8 tw-w-full tw-flex tw-gap-10">
        <div class="tw-flex tw-flex-col tw-flex-1 tw-gap-6">
            @include('dashboard.partials.steps')
        </div>

        <aside class="tw-hidden md:tw-flex tw-flex-col tw-w-[350px] tw-gap-6">
            @include('dashboard.partials.sidebar-progress')
            @include('dashboard.partials.sidebar-next-step')
        </aside>
    </div>

</main>
@endsection
