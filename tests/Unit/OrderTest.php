<?php

namespace Tests\Unit;

use App\Order;
use PHPUnit\Framework\TestCase;

abstract class OrderTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_can_create_order() {

        $data = [
            'origin_lat' => "23",
            'origin_long' => "77",
            'destination_lat' => "24",
            'destination_long' => "76",
            'distance' => "4000",
            'status' => 'UNASSIGNED',
            'created_at' => now(),
            'updated_at' => now()
        ];
        $order = Orders::insert($data);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($order->origin_lat, $data->origin_lat);
    }
}
