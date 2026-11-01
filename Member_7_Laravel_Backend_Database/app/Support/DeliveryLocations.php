<?php

namespace App\Support;

final class DeliveryLocations
{
    /**
     * Curated Ugandan delivery areas used by the checkout dropdowns.
     * The final address/landmark remains free text so customers can give
     * precise directions even when an area is not listed.
     */
    public static function data(): array
    {
        return [
            'Central' => [
                'Kampala' => ['Central Division', 'Kawempe Division', 'Makindye Division', 'Nakawa Division', 'Rubaga Division'],
                'Wakiso' => ['Kira', 'Nansana', 'Wakiso Town', 'Kajjansi', 'Buloba', 'Kakiri', 'Kasangati'],
                'Mukono' => ['Mukono Town', 'Seeta', 'Namanve', 'Katosi', 'Lugazi'],
                'Masaka' => ['Masaka City', 'Nyendo', 'Katwe-Butego', 'Buwunga'],
                'Mpigi' => ['Mpigi Town', 'Buwama', 'Kammengo'],
                'Mityana' => ['Mityana Town', 'Busunju'],
                'Mubende' => ['Mubende Town', 'Kassanda'],
            ],
            'Eastern' => [
                'Jinja' => ['Jinja City', 'Bugembe', 'Masese', 'Walukuba', 'Mpumwire'],
                'Mbale' => ['Mbale City', 'Nkoma', 'Nakaloke'],
                'Iganga' => ['Iganga Town', 'Nakalama', 'Nawandyo'],
                'Busia' => ['Busia Town', 'Lumino'],
                'Tororo' => ['Tororo Town', 'Malaba', 'Mukuju'],
                'Soroti' => ['Soroti City', 'Asuret', 'Katakwi Road Area'],
                'Moroto' => ['Moroto Town'],
            ],
            'Northern' => [
                'Gulu' => ['Gulu City', 'Pece', 'Layibi', 'Bardege-Layibi'],
                'Lira' => ['Lira City', 'Adyel', 'Railway', 'Ojwina'],
                'Arua' => ['Arua City', 'Oli', 'Pajulu', 'River Oli'],
                'Kitgum' => ['Kitgum Town'],
                'Soroti' => ['Soroti City', 'Asuret', 'Katakwi Road Area'],
                'Adjumani' => ['Adjumani Town'],
                'Nebbi' => ['Nebbi Town'],
            ],
            'Western' => [
                'Mbarara' => ['Mbarara City', 'Kakoba', 'Nyamitanga', 'Kakiika'],
                'Fort Portal' => ['Fort Portal City', 'West Division', 'East Division'],
                'Kabale' => ['Kabale Municipality', 'Northern Division', 'Southern Division'],
                'Kasese' => ['Kasese Municipality', 'Nyamwamba', 'Bulembia'],
                'Hoima' => ['Hoima City', 'Kahoora', 'Busiisi'],
                'Bushenyi' => ['Bushenyi-Ishaka', 'Ishaka', 'Bushenyi Town'],
                'Rukungiri' => ['Rukungiri Municipality'],
            ],
        ];
    }

    public static function regions(): array
    {
        return array_keys(self::data());
    }

    public static function districts(string $region): array
    {
        return array_keys(self::data()[$region] ?? []);
    }

    public static function areas(string $region, string $district): array
    {
        return self::data()[$region][$district] ?? [];
    }

    public static function isValid(string $region, string $district, string $area): bool
    {
        return in_array($district, self::districts($region), true)
            && in_array($area, self::areas($region, $district), true);
    }
}
