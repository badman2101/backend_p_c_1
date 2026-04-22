<?php

namespace App\Http\Controllers;

use App\Models\Donthu;
use App\Models\Nguontin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Validator;

class DonthuController extends Controller
{
    /**
     * Danh sách đơn thư (có thể lọc theo trạng thái, phân loại).
     */
    public function index(Request $request)
    {
        $query = Donthu::query()->orderByDesc('id');

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('phan_loai')) {
            $query->where('phan_loai', $request->phan_loai);
        }

        $donthu = $query->get();

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'data' => $donthu,
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Tạo mới một đơn thư.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tieu_de' => 'nullable|string|max:255',
            'phan_loai' => 'nullable|string|max:255',
            'nguon_tin' => 'nullable|string|max:255',
            'information_nguoiguidon' => 'nullable|string|max:255',
            'noi_dung_don' => 'nullable',
            'can_bo_thu_ly' => 'nullable|string|max:255',
            'ket_qua_xu_ly' => 'nullable|string|max:255',
            'ngay_tiep_nhan' => 'nullable|date',
            'han_xu_ly' => 'nullable|date',
            'trang_thai' => 'nullable|string|max:255',
            'can_bo_huong_dan' => 'nullable',
            'kho_khan' =>'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $payload = $this->applyResolvedStatus($validator->validated());
            $payload = $this->applyProcessingDeadline($payload);
            $donthu = Donthu::create($payload);
            $this->createNguonTinIfNeeded($donthu);

            return response()->json([
                'status' => HttpResponse::HTTP_OK,
                'message' => 'Tạo đơn thư thành công',
            ], HttpResponse::HTTP_OK);
        } catch (\Exception $error) {
            return response()->json([
                'status' => HttpResponse::HTTP_BAD_REQUEST,
                'message' => 'Tạo đơn thư thất bại',
                'error' => $error->getMessage(),
            ], HttpResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Xem chi tiết một đơn thư theo id.
     */
    public function show($id)
    {
        $donthu = Donthu::findOrFail($id);

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'data' => $donthu,
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Cập nhật đơn thư theo id.
     */
    public function update(Request $request, $id)
    {
        $donthu = Donthu::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tieu_de' => 'sometimes|nullable|string|max:255',
            'phan_loai' => 'sometimes|nullable|string|max:255',
            'nguon_tin' => 'sometimes|nullable|string|max:255',
            'information_nguoiguidon' => 'sometimes|nullable|string|max:255',
            'noi_dung_don' => 'sometimes|nullable',
            'can_bo_thu_ly' => 'sometimes|nullable|string|max:255',
            'ket_qua_xu_ly' => 'sometimes|nullable|string|max:255',
            'ngay_tiep_nhan' => 'sometimes|nullable|date',
            'han_xu_ly' => 'sometimes|nullable|date',
            'trang_thai' => 'sometimes|nullable|string|max:255',
            'kho_khan' =>'sometimes|nullable',
            'can_bo_huong_dan' => 'sometimes|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $payload = $this->applyResolvedStatus($validator->validated());
        $payload = $this->applyProcessingDeadline($payload);
        $donthu->update($payload);
        $this->createNguonTinIfNeeded($donthu->fresh());

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'message' => 'Cập nhật đơn thư thành công',
            'data' => $donthu->fresh(),
        ], HttpResponse::HTTP_OK);
    }

    /**
     * Nếu kết quả xử lý là "Đưa vào nguồn tin" thì tự động tạo bản ghi nguồn tin.
     */
    private function createNguonTinIfNeeded(Donthu $donthu): void
    {
        if (trim((string) $donthu->ket_qua_xu_ly) !== 'Đưa vào nguồn tin') {
            return;
        }

        Nguontin::create([
            'ngay_phan_cong' => now(),
            'noi_dung' => $donthu->noi_dung_don,
            'dieu_tra_vien' => $donthu->can_bo_thu_ly,
            // 'ket_qua' => $donthu->ket_qua_xu_ly,
            'can_bo_huong_dan' => $donthu->can_bo_huong_dan,
        ]);
    }

    /**
     * Chuẩn hóa ngày phân công theo định dạng d/m/Y trước khi lưu nguồn tin.
     */
    private function formatNgayPhanCong($ngayTiepNhan): ?string
    {
        if (!$ngayTiepNhan) {
            return null;
        }

        try {
            return Carbon::parse($ngayTiepNhan)->format('d/m/Y');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Nếu đã có kết quả xử lý thì tự động chuyển trạng thái sang "Đã giải quyết".
     */
    private function applyResolvedStatus(array $payload): array
    {
        $ketQua = trim((string) ($payload['ket_qua_xu_ly'] ?? ''));

        if ($ketQua !== '') {
            $payload['trang_thai'] = 'Đã giải quyết';
        }

        return $payload;
    }

    /**
     * Hạn xử lý được tính bằng ngày tiếp nhận cộng thêm 2 tháng.
     */
    private function applyProcessingDeadline(array $payload): array
    {
        if (empty($payload['ngay_tiep_nhan'])) {
            return $payload;
        }

        $payload['han_xu_ly'] = Carbon::parse($payload['ngay_tiep_nhan'])->addMonthsNoOverflow(2);

        return $payload;
    }

    /**
     * Xóa đơn thư theo id.
     */
    public function destroy($id)
    {
        $donthu = Donthu::findOrFail($id);
        $donthu->delete();

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'message' => 'Xóa đơn thư thành công',
        ], HttpResponse::HTTP_OK);
    }
}
