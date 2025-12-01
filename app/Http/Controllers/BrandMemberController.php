<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandMemberController extends Controller
{
    /**
     * Display a listing of the brand members.
     */
    public function index(Brand $brand): View
    {
        $this->authorize('view', $brand);

        $members = $brand->members()
            ->with('user', 'inviter')
            ->orderByRaw("FIELD(role, 'admin', 'editor', 'member')")
            ->get();

        $isAdmin = $brand->isAdmin(auth()->user());

        return view('brands.members.index', compact('brand', 'members', 'isAdmin'));
    }

    /**
     * Show the form for inviting a new member.
     */
    public function create(Brand $brand): View
    {
        $this->authorize('manageMembers', $brand);

        return view('brands.members.create', compact('brand'));
    }

    /**
     * Store a newly invited member.
     */
    public function store(Request $request, Brand $brand): RedirectResponse
    {
        $this->authorize('manageMembers', $brand);

        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,editor,member',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'role.required' => 'Vui lòng chọn quyền.',
            'role.in' => 'Quyền không hợp lệ.',
        ]);

        // Find user by email
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Không tìm thấy người dùng với email này.']);
        }

        // Check if user is already a member
        if ($brand->isMember($user)) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Người dùng này đã là thành viên của thương hiệu.']);
        }

        // Add member
        BrandMember::create([
            'brand_id' => $brand->id,
            'user_id' => $user->id,
            'role' => $validated['role'],
            'invited_by' => auth()->id(),
            'joined_at' => now(),
        ]);

        return redirect()->route('brands.members.index', $brand)
            ->with('success', __('messages.brand.member_added'));
    }

    /**
     * Update the member's role.
     */
    public function update(Request $request, Brand $brand, BrandMember $member): RedirectResponse
    {
        $this->authorize('manageMembers', $brand);

        // Ensure member belongs to this brand
        if ($member->brand_id !== $brand->id) {
            abort(404);
        }

        // Cannot change own role
        if ($member->user_id === auth()->id()) {
            return back()->withErrors(['role' => 'Bạn không thể thay đổi quyền của chính mình.']);
        }

        // Cannot demote the brand owner
        if ($member->user_id === $brand->created_by && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Không thể thay đổi quyền của người tạo thương hiệu.']);
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,editor,member',
        ]);

        $member->update(['role' => $validated['role']]);

        return redirect()->route('brands.members.index', $brand)
            ->with('success', __('messages.brand.member_updated'));
    }

    /**
     * Remove a member from the brand.
     */
    public function destroy(Brand $brand, BrandMember $member): RedirectResponse
    {
        $this->authorize('manageMembers', $brand);

        // Ensure member belongs to this brand
        if ($member->brand_id !== $brand->id) {
            abort(404);
        }

        // Cannot remove yourself
        if ($member->user_id === auth()->id()) {
            return back()->withErrors(['member' => __('messages.brand.cannot_remove_self')]);
        }

        // Cannot remove brand owner
        if ($member->user_id === $brand->created_by) {
            return back()->withErrors(['member' => __('messages.brand.cannot_remove_owner')]);
        }

        $member->delete();

        return redirect()->route('brands.members.index', $brand)
            ->with('success', __('messages.brand.member_removed'));
    }
}
