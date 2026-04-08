<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donvi; // Sửa App\Model -> App\Models

class DonviSeeder extends Seeder
{
    public function run()
    {
        $donvis = [
            'PC01', 'Thành Đông', 'Ái Quốc', 'Hải Dương',
            'Lê Thanh Nghị', 'Việt Hòa', 'Nam Đồng', 'Tân Hưng',
            'Thạch Khôi', 'Tứ Minh', 'Chu Văn An', 'Chí Linh',
            'Trần Hưng Đạo', 'Nguyễn Trãi', 'Trần Nhân Tông', 'Lê Đại Hành',
            'Phú Thái', 'Lai Khê', 'An Thành', 'Kim Thành',
            'Kinh Môn', 'Nguyễn Đại Năng', 'Trần Liễu', 'Bắc An Phụ',
            'Phạm Sư Mệnh', 'Nhị Chiểu', 'Nam An Phụ', 'Nam Sách',
            'Thái Tân', 'Hợp Tiến', 'Trần Phú', 'An Phú',
        ];

        foreach ($donvis as $ten) {
            Donvi::updateOrCreate(
                ['ten_don_vi' => $ten], // Điều kiện tìm kiếm
                ['ten_don_vi' => $ten]  // Dữ liệu cập nhật/tạo mới
            );
        }
    }
}