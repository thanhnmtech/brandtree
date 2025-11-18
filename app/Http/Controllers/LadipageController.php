<?php

namespace App\Http\Controllers;

use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LadipageController extends Controller
{

    public function store(Request $request)
    {
        //check secret key
        if ($request->secret_key != config('app.ladipage.secret_key')) {
            return response()->json(['code' => 401, 'msg' => 'Secret Key không hợp lệ'], 401);
        }

        //check api key
        if ($request->api_key != config('app.ladipage.api_key')) {
            return response()->json(['code' => 401, 'msg' => 'Mã tích hợp không hợp lệ'], 401);
        }

        if (empty($request->ladiID)) {
            return response()->json(['code' => 401, 'msg' => 'ladiID empty'], 401);
        }

        if (empty($request->title) || empty($request->slug) || empty($request->html)) {
            return response()->json(['code' => 401, 'msg' => 'content empty'], 401);
        }

        $pages = [
            'home-page' => ['slug' => 'home-page', 'locale' => 'en'],
            'trang-chu' => ['slug' => 'trang-chu', 'locale' => 'vi'],
        ];
        if (!$this->is_slug($request->slug) || !in_array($request->slug, array_keys($pages))) {
            return response()->json(['code' => 401, 'msg' => 'slug invalid'], 401);
        }

        DB::beginTransaction();
        try {
            $page = PageContent::updateOrCreate(
                ['type' => 'homepage'],
                [
                    'type' => 'homepage',
                    'status' => 'published',
                ]
            );

            // Lấy locale hiện tại cần cập nhật
            $locale = $pages[$request->slug]['locale'];

            // Gắn bản dịch vào model
            $page->translateOrNew($locale)->title   = $request->title;
            $page->translateOrNew($locale)->slug    = $pages[$request->slug]['slug'];
            $page->translateOrNew($locale)->content = $request->html;

            // Lưu cả model + translation
            $page->save();
            DB::commit();

            return response()->json([
                'code' => 200,
                'id' => $page->id
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 500,
                'msg' => 'Lỗi khi lưu dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function is_slug($str)
    {
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/i', $str);
    }
}
