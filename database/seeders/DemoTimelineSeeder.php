<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Property;
use App\Models\RentalRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * بيانات تجريبية بس: بيوزّع تواريخ الإنشاء على آخر 9 شهور (أكتر نشاط في الأحدث)
 * وبيحط مشاهدات للعقارات، عشان رسوم لوحة الأدمن يبقى ليها معنى وقت التجربة.
 */
class DemoTimelineSeeder extends Seeder
{
    private const DAYS = 270;

    public function run(): void
    {
        mt_srand(11);

        $userAge = [];
        foreach (User::where('role', '!=', 'admin')->get() as $u) {
            $userAge[$u->id] = $this->age(self::DAYS);
            $this->stamp('users', $u->id, $userAge[$u->id]);
        }

        $propAge = [];
        foreach (Property::withTrashed()->get() as $p) {
            $propAge[$p->id] = $this->age(min(self::DAYS, ($userAge[$p->owner_id] ?? self::DAYS) + 1), 1.1);
            $this->stamp('properties', $p->id, $propAge[$p->id]);
            DB::table('properties')->where('id', $p->id)->update(['views' => mt_rand(15, 120) * max(1, (int) ceil($propAge[$p->id] / 25))]);
        }

        foreach (RentalRequest::all() as $r) {
            $age = $this->age(max(1, $propAge[$r->property_id] ?? 60), 1.0);
            if ($r->accepted_at) {
                $age = max($age, 16 + mt_rand(0, 20)); // الاتفاقات محتاجة وقت قبل القبول والسداد
            }
            $created = now()->subDays($age)->setTime(mt_rand(8, 22), mt_rand(0, 59));
            $row = ['created_at' => $created, 'updated_at' => $created];

            if ($r->accepted_at) {
                $accepted = $created->copy()->addDays(mt_rand(1, 12))->min(now());
                $row['accepted_at'] = $accepted;
                $row['updated_at'] = $accepted;
                foreach (['owner', 'tenant'] as $party) {
                    if ($r->{$party.'_commission_paid_at'}) {
                        $row[$party.'_commission_paid_at'] = $accepted->copy()->addDays(mt_rand(0, 6))->min(now());
                    }
                }
                if ($r->completed_at) {
                    $row['completed_at'] = collect([$row['owner_commission_paid_at'] ?? $accepted, $row['tenant_commission_paid_at'] ?? $accepted])->max();
                    $row['updated_at'] = $row['completed_at'];
                }
            }
            DB::table('rental_requests')->where('id', $r->id)->update($row);
        }

        foreach (ContactMessage::all() as $m) {
            $this->stamp('contact_messages', $m->id, $this->age(60));
        }
    }

    /** عمر بالأيام: كل ما الأُس كبر، النشاط بيتجمع أكتر ناحية النهارده */
    private function age(int $max, float $skew = 1.15): int
    {
        return (int) floor(pow(mt_rand() / mt_getrandmax(), $skew) * $max);
    }

    private function stamp(string $table, int $id, int $age): void
    {
        $t = now()->subDays($age)->setTime(mt_rand(8, 22), mt_rand(0, 59));
        DB::table($table)->where('id', $id)->update(['created_at' => $t, 'updated_at' => $t]);
    }
}
