<?php

namespace App\Http\Controllers;

use App\Models\Vuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VuanController extends Controller
{
    /**
     * Danh sách vụ án (tìm kiếm tùy chọn).
     *
     * Query: q (string) — lọc theo noi_dung, thong_tin_bi_can, ket_qua, can_bo_thu_ly, can_bo_huong_dan
     */
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = Vuan::query()->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('noi_dung', 'like', "%{$q}%")
                    ->orWhere('thong_tin_bi_can', 'like', "%{$q}%")
                    ->orWhere('ket_qua', 'like', "%{$q}%")
                    ->orWhere('can_bo_thu_ly', 'like', "%{$q}%")
                    ->orWhere('can_bo_huong_dan', 'like', "%{$q}%");
            });
        }

        return response()->json([
            'message' => 'OK',
            'data' => $query->get(),
        ]);
    }

    /**
     * Chi tiết một vụ án.
     */
    public function show(int $id): JsonResponse
    {
        $vuan = Vuan::find($id);

        if (!$vuan) {
            return response()->json([
                'message' => 'Không tìm thấy vụ án',
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $vuan,
        ]);
    }

    /**
     * Tạo mới vụ án.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ngay_khoi_to' => ['nullable', 'date'],
            'noi_dung' => ['nullable', 'string'],
            'so_luong_bi_can' => ['nullable', 'string', 'max:255'],
            'thong_tin_bi_can' => ['nullable'],
            'can_bo_thu_ly' => ['nullable', 'string', 'max:255'],
            'can_bo_huong_dan' => ['nullable', 'string', 'max:255'],
            'ket_qua' => ['nullable', 'string', 'max:255'],
            'kho_khan' => ['nullable', 'string', 'max:255'],
        ]);

        $vuan = Vuan::create($validated);

        return response()->json([
            'message' => 'Tạo vụ án thành công',
            'data' => $vuan,
        ], 201);
    }

    /**
     * Cập nhật vụ án.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $vuan = Vuan::find($id);

        if (!$vuan) {
            return response()->json([
                'message' => 'Không tìm thấy vụ án',
            ], 404);
        }

        $validated = $request->validate([
            'ngay_khoi_to' => ['sometimes', 'nullable', 'date'],
            'noi_dung' => ['sometimes', 'nullable', 'string'],
            'so_luong_bi_can' => ['sometimes', 'nullable', 'string', 'max:255'],
            'thong_tin_bi_can' => ['sometimes', 'nullable', 'string'],
            'can_bo_thu_ly' => ['sometimes', 'nullable', 'string', 'max:255'],
            'can_bo_huong_dan' => ['sometimes', 'nullable', 'string', 'max:255'],
            'ket_qua' => ['sometimes', 'nullable', 'string', 'max:255'],
            'kho_khan' => ['sometimes', 'nullable', 'string'],
        ]);

        $vuan->fill($validated);
        $vuan->save();

        return response()->json([
            'message' => 'Cập nhật vụ án thành công',
            'data' => $vuan,
        ]);
    }

    /**
     * Xóa vụ án.
     */
    public function destroy(int $id): JsonResponse
    {
        $vuan = Vuan::find($id);

        if (!$vuan) {
            return response()->json([
                'message' => 'Không tìm thấy vụ án',
            ], 404);
        }

        $vuan->delete();

        return response()->json([
            'message' => 'Xóa vụ án thành công',
        ]);
    }
}
