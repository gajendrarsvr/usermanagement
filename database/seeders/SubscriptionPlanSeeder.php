<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscriptionPlanTypes= [
            ['id' => '1', 'plan_type' => '3 Year'],
            ['id' => '2', 'plan_type' => '6 Year'],
            ['id' => '3', 'plan_type' => '25 Year '],
            ['id' => '4', 'plan_type' => '2 Year '],
            ['id' => '5', 'plan_type' => '15 Year '],
        ];
        foreach ($subscriptionPlanTypes as $subscriptionPlanType) {
            SubscriptionPlan::create($subscriptionPlanType);
        }
    }
}
