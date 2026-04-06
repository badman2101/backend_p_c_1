<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Nguontin;

class NguonTinController extends Controller
{
    /**
     * Danh sách nguồn tin (không phân trang, có tìm kiếm tùy chọn).
     * Query params:
     * - q (string): tìm trong noi_dung, dieu_tra_vien, ket_qua, ngay_phan_cong
     */
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = Nguontin::query()->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('noi_dung', 'like', "%{$q}%")
                    ->orWhere('dieu_tra_vien', 'like', "%{$q}%")
                    ->orWhere('ket_qua', 'like', "%{$q}%")
                    ->orWhere('ngay_phan_cong', 'like', "%{$q}%");
            });
        }

        return response()->json([
            'message' => 'OK',
            'data' => $query->get(),
        ]);
    }

    /**
     * Xem chi tiết 1 nguồn tin.
     */
    public function show(int $id): JsonResponse
    {
        $nguonTin = Nguontin::find($id);

        if (!$nguonTin) {
            return response()->json([
                'message' => 'Không tìm thấy nguồn tin',
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $nguonTin,
        ]);
    }

    /**
     * Tạo mới nguồn tin.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ngay_phan_cong' => ['required', 'string', 'max:255'],
            'noi_dung' => ['required'],
            'dieu_tra_vien' => ['required', 'string', 'max:255'],
            'ket_qua' => ['max:255'],
        ]);

        $nguonTin = Nguontin::create($validated);

        return response()->json([
            'message' => 'Tạo nguồn tin thành công',
            'data' => $nguonTin,
        ], 201);
    }

    /**
     * Cập nhật nguồn tin.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $nguonTin = Nguontin::find($id);

        if (!$nguonTin) {
            return response()->json([
                'message' => 'Không tìm thấy nguồn tin',
            ], 404);
        }

        $validated = $request->validate([
            'ngay_phan_cong' => ['sometimes', 'required', 'string', 'max:255'],
            'noi_dung' => ['sometimes', 'required'],
            'dieu_tra_vien' => ['sometimes', 'required', 'string', 'max:255'],
            'ket_qua' => ['max:255'],
        ]);

        $nguonTin->fill($validated);
        $nguonTin->save();

        return response()->json([
            'message' => 'Cập nhật nguồn tin thành công',
            'data' => $nguonTin,
        ]);
    }

    /**
     * Xóa nguồn tin.
     */
    public function destroy(int $id): JsonResponse
    {
        $nguonTin = Nguontin::find($id);

        if (!$nguonTin) {
            return response()->json([
                'message' => 'Không tìm thấy nguồn tin',
            ], 404);
        }

        $nguonTin->delete();

        return response()->json([
            'message' => 'Xóa nguồn tin thành công',
        ]);
    }
}
