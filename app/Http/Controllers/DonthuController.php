<?php

namespace App\Http\Controllers;

use App\Models\Donthu;
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
            'noi_dung_don' => 'nullable|string|max:255',
            'can_bo_thu_ly' => 'nullable|string|max:255',
            'ket_qua_xu_ly' => 'nullable|string|max:255',
            'ngay_tiep_nhan' => 'nullable|date',
            'han_xu_ly' => 'nullable|date',
            'trang_thai' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            Donthu::create($validator->validated());

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
            'noi_dung_don' => 'sometimes|nullable|string|max:255',
            'can_bo_thu_ly' => 'sometimes|nullable|string|max:255',
            'ket_qua_xu_ly' => 'sometimes|nullable|string|max:255',
            'ngay_tiep_nhan' => 'sometimes|nullable|date',
            'han_xu_ly' => 'sometimes|nullable|date',
            'trang_thai' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $donthu->update($validator->validated());

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'message' => 'Cập nhật đơn thư thành công',
            'data' => $donthu->fresh(),
        ], HttpResponse::HTTP_OK);
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
