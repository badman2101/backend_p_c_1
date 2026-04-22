<?php

namespace App\Http\Controllers;

use App\Models\Vuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VuanController extends Controller
{
    /**
     * Tính hạn xử lý theo phân loại và ngày khởi tố.
     */
    private function calculateHanXuLy(?string $ngayKhoiTo, ?string $phanLoai): ?string
    {
        if (!$ngayKhoiTo || !$phanLoai) {
            return null;
        }

        $monthsByPhanLoai = [
            'Tội phạm ít nghiêm trọng' => 2,
            'Tội phạm nghiêm trọng' => 3,
            'Tội phạm rất nghiêm trọng' => 4,
            'Tội phạm đặc biệt nghiêm trọng' => 4,
        ];

        if (!isset($monthsByPhanLoai[$phanLoai])) {
            return null;
        }

        return Carbon::parse($ngayKhoiTo)
            ->addMonths($monthsByPhanLoai[$phanLoai])
            ->toDateString();
    }

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
                    ->orWhere('can_bo_huong_dan', 'like', "%{$q}%")
                    ->orWhere('bien_phap_ngan_chan', 'like', "%{$q}%");
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
            'phan_loai' => ['nullable', 'string', 'max:255'],
            'so_luong_bi_can' => ['nullable', 'string', 'max:255'],
            'thong_tin_bi_can' => ['nullable'],
            'can_bo_thu_ly' => ['nullable', 'string', 'max:255'],
            'can_bo_huong_dan' => ['nullable', 'string', 'max:255'],
            'ket_qua' => ['nullable', 'string', 'max:255'],
            'kho_khan' => ['nullable', 'string'],
            'bien_phap_ngan_chan' => ['nullable'],
        ]);

        $validated['han_xu_ly'] = $this->calculateHanXuLy(
            $validated['ngay_khoi_to'] ?? null,
            $validated['phan_loai'] ?? null
        );

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
            'phan_loai' => ['sometimes', 'nullable', 'string', 'max:255'],
            'so_luong_bi_can' => ['sometimes', 'nullable', 'string', 'max:255'],
            'thong_tin_bi_can' => ['sometimes', 'nullable', 'string'],
            'can_bo_thu_ly' => ['sometimes', 'nullable', 'string', 'max:255'],
            'can_bo_huong_dan' => ['sometimes', 'nullable', 'string', 'max:255'],
            'ket_qua' => ['sometimes', 'nullable', 'string', 'max:255'],
            'kho_khan' => ['sometimes', 'nullable', 'string'],
            'bien_phap_ngan_chan' => ['sometimes','nullable'],
        ]);

        $ngayKhoiTo = array_key_exists('ngay_khoi_to', $validated)
            ? $validated['ngay_khoi_to']
            : $vuan->ngay_khoi_to?->toDateString();

        $phanLoai = array_key_exists('phan_loai', $validated)
            ? $validated['phan_loai']
            : $vuan->phan_loai;

        $validated['han_xu_ly'] = $this->calculateHanXuLy($ngayKhoiTo, $phanLoai);

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
