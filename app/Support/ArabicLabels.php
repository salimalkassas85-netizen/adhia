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
            'pending' => 'انتظار',
            'approved' => 'تم المعالجة',
            'assigned' => 'تم المعالجة',
            'gift_received_by_agent' => 'تم المعالجة',
            'on_the_way' => 'تم المعالجة',
            'delivered' => 'تم تسليم الأمانة',
            'failed' => 'انتظار',
            'cancelled' => 'انتظار',
            'confirmed' => 'انتظار',
            'received' => 'تم الاستلام',
            'allocated' => 'تم الاستلام',
            'in_distribution' => 'تم الاستلام',
            'completed' => 'تم تسليم الهدية',
        ];
    }

    public static function beneficiaryStatusOptions(): array
    {
        return [
            'pending' => 'انتظار',
            'approved' => 'تم المعالجة',
            'delivered' => 'تم تسليم الأمانة',
        ];
    }

    public static function donationStatusOptions(): array
    {
        return [
            'pending' => 'جاري المعالجة',
            'received' => 'تم الاستلام',
            'completed' => 'تم التسليم',
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

    public static function socialStatus(?string $status): string
    {
        return self::socialStatusOptions()[$status] ?? (string) $status;
    }

    public static function socialStatusOptions(): array
    {
        return [
            'married' => 'متزوج/ة',
            'widowed' => 'أرمل/ة',
            'divorced' => 'مطلق/ة',
            'single' => 'أعزب/عزباء',
            'other' => 'غير ذلك',
        ];
    }
}
