<?php

namespace App\Http\Controllers;

use App\Models\Complains;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Validator;

class ComplainsController extends Controller
{
    public function index(Request $request)
    {
        $query = Complains::query()->with('assignedTo');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $complains = $query->orderByDesc('id')->get();

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'data' => $complains,
        ], HttpResponse::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'type' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        try {
            //code..
            Complains::create([
                'title' => $request->title,
                'content' => $request->content,
                'type' => $request->type,
                'assigned_to' => $request->assigned_to,
                'status' => $request->status,
            ]);
            return response()->json([
                'status' => HttpResponse::HTTP_OK,
                'message' => 'Tạo khiếu nại thành công',
            ], HttpResponse::HTTP_OK);
        } catch (\Exception $error) {
            return response()->json([
                'status' => HttpResponse::HTTP_BAD_REQUEST,
                'message' => 'Tạo khiếu nại thất bại',
                'error' => $error,
            ], HttpResponse::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        $complain = Complains::with('assignedTo')->findOrFail($id);

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'data' => $complain,
        ], HttpResponse::HTTP_OK);
    }       

    public function update(Request $request, $id)
    {
        $complain = Complains::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
            'type' => 'sometimes|required|string',
            'assigned_to' => 'sometimes|required|exists:users,id',
            'status' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $payload = $validator->validated();
        $complain->update($payload);

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'message' => 'Cập nhật khiếu nại thành công',
            'data' => $complain->fresh()->load('assignedTo'),
        ], HttpResponse::HTTP_OK);
    }

    public function destroy($id)
    {
        $complain = Complains::findOrFail($id);
        $complain->delete();

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'message' => 'Xóa khiếu nại thành công',
        ], HttpResponse::HTTP_OK);
    }

    public function assignTo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $complain = Complains::findOrFail($id);
        $complain->assigned_to = $request->user_id;
        $complain->save();

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'message' => 'Phân công người xử lý thành công',
            'data' => $complain->fresh()->load('assignedTo'),
        ], HttpResponse::HTTP_OK);
    }

    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $complain = Complains::findOrFail($id);
        $complain->status = $request->status;
        $complain->save();

        return response()->json([
            'status' => HttpResponse::HTTP_OK,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $complain->fresh()->load('assignedTo'),
        ], HttpResponse::HTTP_OK);
    }
}
