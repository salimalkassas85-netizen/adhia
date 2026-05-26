<?php

namespace App\Support;

class ArabicLabels
{
    public static function status(?string $status): string
    {
        return self::statuses()[$status] ?? (string) $status;
    }

    public static function donationType(?string $type): string
    {
        return self::donationTypes()[$type] ?? (string) $type;
    }

    public static function donationScope(?string $scope): string
    {
        return self::donationScopes()[$scope] ?? (string) $scope;
    }

    public static function statuses(): array
    {
        return [
            'pending' => 'بانتظار المراجعة',
            'approved' => 'معتمد',
            'assigned' => 'مسند لفريق التوزيع',
            'gift_received_by_agent' => 'استلمها فريق التوزيع',
            'on_the_way' => 'في الطريق',
            'delivered' => 'تم التسليم',
            'failed' => 'تعذر التسليم',
            'cancelled' => 'ملغي',
            'confirmed' => 'مؤكد',
            'received' => 'تم الاستلام',
            'allocated' => 'مخصص',
            'in_distribution' => 'قيد التوزيع',
            'completed' => 'مكتمل',
        ];
    }

    public static function donationTypes(): array
    {
        return [
            'meat_kg' => 'لحم بالكيلو',
            'money' => 'مبلغ مالي',
            'sacrifice_share' => 'سهم أضحية',
            'full_sacrifice' => 'أضحية كاملة',
        ];
    }

    public static function donationScopes(): array
    {
        return [
            'own_area' => 'منطقته',
            'selected_area' => 'منطقة مختارة',
            'most_needed' => 'الأكثر احتياجًا للتغطية',
        ];
    }
}
