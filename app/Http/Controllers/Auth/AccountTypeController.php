<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountTypeController extends Controller
{
    /**
     * Hiển thị form chọn loại tài khoản (Sinh Viên / Doanh Nghiệp)
     */
    public function show()
    {
        return view('auth.account-type');
    }

    /**
     * Lưu loại tài khoản đã chọn
     */
    public function store(Request $request)
    {
        // Validate loại tài khoản phải là student hoặc business
        $request->validate([
            'account_type' => ['required', 'in:student,business'],
        ], [
            'account_type.required' => 'Vui lòng chọn loại tài khoản.',
            'account_type.in' => 'Loại tài khoản không hợp lệ.',
        ]);

        // Cập nhật account_type cho user hiện tại
        $user = Auth::user();
        $user->account_type = $request->account_type;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Chào mừng bạn đến với AI Cây Thương Hiệu!');
    }
}
