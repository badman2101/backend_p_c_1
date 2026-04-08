<?php

namespace App\Http\Controllers;

use App\Models\Donvi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonviController extends Controller
{
    /**
     * Danh sách đơn vị (tìm kiếm tùy chọn theo tên).
     *
     * Query: q (string) — lọc theo ten_don_vi
     */
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $query = Donvi::query()->orderByDesc('id');

        if ($q !== '') {
            $query->where('ten_don_vi', 'like', "%{$q}%");
        }

        return response()->json([
            'message' => 'OK',
            'data' => $query->get(),
        ]);
    }

    /**
     * Chi tiết một đơn vị.
     */
    public function show(int $id): JsonResponse
    {
        $donvi = Donvi::find($id);

        if (!$donvi) {
            return response()->json([
                'message' => 'Không tìm thấy đơn vị',
            ], 404);
        }

        return response()->json([
            'message' => 'OK',
            'data' => $donvi,
        ]);
    }

    /**
     * Tạo mới đơn vị.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ten_don_vi' => ['required', 'string', 'max:255'],
        ]);

        $donvi = Donvi::create($validated);

        return response()->json([
            'message' => 'Tạo đơn vị thành công',
            'data' => $donvi,
        ], 201);
    }

    /**
     * Cập nhật đơn vị.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $donvi = Donvi::find($id);

        if (!$donvi) {
            return response()->json([
                'message' => 'Không tìm thấy đơn vị',
            ], 404);
        }

        $validated = $request->validate([
            'ten_don_vi' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $donvi->fill($validated);
        $donvi->save();

        return response()->json([
            'message' => 'Cập nhật đơn vị thành công',
            'data' => $donvi,
        ]);
    }

    /**
     * Xóa đơn vị.
     */
    public function destroy(int $id): JsonResponse
    {
        $donvi = Donvi::find($id);

        if (!$donvi) {
            return response()->json([
                'message' => 'Không tìm thấy đơn vị',
            ], 404);
        }

        $donvi->delete();

        return response()->json([
            'message' => 'Xóa đơn vị thành công',
        ]);
    }
}
