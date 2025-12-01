<x-app-layout>
    <x-slot name="header">
        <div class="tw-flex tw-items-center tw-justify-between">
            <div class="tw-flex tw-items-center tw-gap-4">
                <a href="{{ route('brands.index') }}" class="tw-text-gray-500 hover:tw-text-gray-700 tw-transition">
                    <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h2 class="tw-text-2xl md:tw-text-3xl tw-font-bold tw-text-gray-800">Thành viên</h2>
                    <p class="tw-text-sm tw-text-gray-500">{{ $brand->name }}</p>
                </div>
            </div>
            @if($isAdmin)
                <a href="{{ route('brands.members.create', $brand) }}" class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-bg-[#16a249] tw-text-white tw-rounded-lg tw-font-medium hover:tw-bg-[#138a3e] tw-transition">
                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Thêm thành viên
                </a>
            @endif
        </div>
    </x-slot>

    @if(session('success'))
        <div class="tw-mb-6 tw-bg-green-100 tw-border tw-border-green-400 tw-text-green-700 tw-px-4 tw-py-3 tw-rounded-lg">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="tw-mb-6 tw-bg-red-100 tw-border tw-border-red-400 tw-text-red-700 tw-px-4 tw-py-3 tw-rounded-lg">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-6">
        <!-- Sidebar -->
        <div class="lg:tw-w-64 tw-flex-shrink-0">
            @include('brands.partials.sidebar', ['brand' => $brand])
        </div>

        <!-- Main Content -->
        <div class="tw-flex-1 tw-min-w-0">
            <!-- Members List -->
    <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-overflow-hidden">
        <div class="tw-divide-y tw-divide-gray-100">
            @forelse($members as $member)
                <div class="tw-p-4 tw-flex tw-items-center tw-justify-between hover:tw-bg-gray-50">
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <div class="tw-w-12 tw-h-12 tw-bg-[#16a249] tw-rounded-full tw-flex tw-items-center tw-justify-center">
                            <span class="tw-text-white tw-font-semibold">{{ strtoupper(substr($member->user->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <h3 class="tw-font-semibold tw-text-gray-800">{{ $member->user->name }}</h3>
                            <p class="tw-text-sm tw-text-gray-500">{{ $member->user->email }}</p>
                            @if($member->inviter && $member->user_id !== $brand->created_by)
                                <p class="tw-text-xs tw-text-gray-400">Mời bởi {{ $member->inviter->name }} - {{ $member->joined_at?->format('d/m/Y') }}</p>
                            @elseif($member->user_id === $brand->created_by)
                                <p class="tw-text-xs tw-text-gray-400">Người tạo thương hiệu</p>
                            @endif
                        </div>
                    </div>
                    <div class="tw-flex tw-items-center tw-gap-3">
                        @if($isAdmin && $member->user_id !== auth()->id())
                            <form action="{{ route('brands.members.update', [$brand, $member]) }}" method="POST" class="tw-flex tw-items-center tw-gap-2">
                                @csrf
                                @method('PUT')
                                <select name="role" onchange="this.form.submit()" class="tw-text-sm tw-border tw-border-gray-300 tw-rounded-lg tw-px-3 tw-py-1.5 focus:tw-ring-2 focus:tw-ring-[#16a249] focus:tw-border-transparent {{ $member->user_id === $brand->created_by ? 'tw-opacity-50 tw-cursor-not-allowed' : '' }}" {{ $member->user_id === $brand->created_by ? 'disabled' : '' }}>
                                    {{-- <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option> --}}
                                    <option value="editor" {{ $member->role === 'editor' ? 'selected' : '' }}>Editor</option>
                                    <option value="member" {{ $member->role === 'member' ? 'selected' : '' }}>Member</option>
                                </select>
                            </form>
                            @if($member->user_id !== $brand->created_by)
                                <form action="{{ route('brands.members.destroy', [$brand, $member]) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thành viên này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tw-p-2 tw-text-red-600 hover:tw-bg-red-50 tw-rounded-lg tw-transition" title="Xóa thành viên">
                                        <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endif
                        @else
                            <span class="tw-px-3 tw-py-1 tw-text-sm tw-font-medium tw-rounded-full {{ $member->role === 'admin' ? 'tw-bg-purple-100 tw-text-purple-700' : ($member->role === 'editor' ? 'tw-bg-blue-100 tw-text-blue-700' : 'tw-bg-gray-100 tw-text-gray-700') }}">
                                {{ ucfirst($member->role) }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="tw-p-8 tw-text-center tw-text-gray-500">Chưa có thành viên nào.</div>
            @endforelse
        </div>
    </div>
        </div>
    </div>
</x-app-layout>
