<?php

namespace App\Support;

/**
 * بيانات جغرافية لكل محافظات مصر (مدن + أحياء + إحداثيات تقريبية)
 * كل محافظة في ملف مستقل داخل database/data/egypt
 */
class EgyptData
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (glob(database_path('data/egypt/*.php')) as $file) {
                $g = require $file;
                self::$cache[$g['name']] = $g;
            }
        }

        return self::$cache;
    }

    public static function governorates(): array
    {
        return array_keys(self::all());
    }

    public static function cities(string $gov): array
    {
        return array_keys(self::all()[$gov]['cities'] ?? []);
    }

    public static function districts(string $gov, string $city): array
    {
        return self::all()[$gov]['cities'][$city]['districts'] ?? [];
    }

    /** موقع عشوائي حقيقي الشكل: محافظة > مدينة > حي + إحداثيات */
    public static function random(?string $gov = null): array
    {
        $all = self::all();
        $g = $all[$gov ?? array_rand($all)];
        $city = array_rand($g['cities']);
        $c = $g['cities'][$city];
        $districts = $c['districts'];

        return [
            'governorate' => $g['name'],
            'city' => $city,
            'district' => $districts[array_rand($districts)],
            'latitude' => round($c['lat'] + mt_rand(-900, 900) / 10000, 7),
            'longitude' => round($c['lng'] + mt_rand(-900, 900) / 10000, 7),
        ];
    }
}
