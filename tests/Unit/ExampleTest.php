<?php

namespace Tests\Unit;

use App\Http\Controllers\OrderController;
use App\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Mockery\Mock;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    protected $orderServiceMock;
    protected $orderController;
    public function setUp(): void
    {
        parent::setUp();
        $this->orderServiceMock = \Mockery::mock('App\Http\Services\OrderService');
        //$this->orderController = $this->app->instance(OrderController::class, new OrderController($this->orderServiceMock));
    }

    public function test_can_create_order() {

        $params = [
            "origin"=> ["28.704060", "76.102493"],
            "destination"=>["27.535517", "77.391029"]
        ];
        $expectedResponse = [
            'id' => 1,
            'distance' => '23002',
            'status' => 'UNASSIGNED'
        ];

        $request = new Request($params);
        $this->orderServiceMock->shouldReceive('createOrder')->andReturn($expectedResponse);

        $response = new OrderController($this->orderServiceMock);
        $result = $response->createOrder($request);
        $result = (array) $result->getData();
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('distance', $result);
        $this->assertArrayHasKey('status', $result);
    }

    public function test_can_get_orders_success() {

        $params = [
            "page"=> 1,
            "limit"=> 1
        ];
        $expectedResponse = [
            [
                'id' => 1,
                'distance' => '23002',
                'status' => 'TAKEN'
            ]
        ];

        $request = new Request($params);
        $this->orderServiceMock->shouldReceive('getOrders')->andReturn($expectedResponse);

        $response = new OrderController($this->orderServiceMock);
        $result = $response->getOrders($request);
        $result = (array) $result->getData();
        $this->assertIsArray($result);
        foreach ($result as $item) {
            $item =(array) $item;
            $this->assertArrayHasKey('id',$item);
            $this->assertArrayHasKey('distance',$item);
            $this->assertArrayHasKey('status',$item);
        }
    }

    public function test_can_get_orders_failure() {

        $params = [
            "page"=> 1,
            "limit"=> 0
        ];
        $expectedResponse = [
            'error' => 'Page number and limit must be equal to or greater than 1'
        ];

        $request = new Request($params);
        $this->orderServiceMock->shouldReceive('getOrders')->andReturn($expectedResponse);

        $response = new OrderController($this->orderServiceMock);
        $result = $response->getOrders($request);
        $result = (array) $result->getData();
        $this->assertArrayHasKey('error',$result);
    }

    public function test_can_get_orders_empty() {

        $params = [
            "page"=> 1,
            "limit"=> 1
        ];
        $expectedResponse = [];

        $request = new Request($params);
        $this->orderServiceMock->shouldReceive('getOrders')->andReturn($expectedResponse);

        $response = new OrderController($this->orderServiceMock);
        $result = $response->getOrders($request);
        $result = (array) $result->getData();
        $this->assertEquals([],$result);
    }

    public function test_can_update_order_success() {

        $id = 2;
        $params = [
            "_method"=> "put",
            "status" => "TAKEN"
        ];
        $expectedResponse = [
            'status' => 'SUCCESS'
        ];

        $mock = $this->orderServiceMock;
        $request = new Request($params);

        $mock->shouldReceive('updateOrder')->andReturn($expectedResponse);

        $response = new OrderController($mock);
        $result = $response->updateOrder($request, $id);
        $result = (array) $result->getData();
        $this->assertArrayHasKey('status', $result);
    }

    public function test_can_update_order_failure() {

        $id = 2;
        $params = [
            "_method"=> "put",
            "status" => "TAKEN"
        ];

        $expectedResult = [
            'error' => 'Order already taken'
        ];

        $request = new Request($params);

        $this->orderServiceMock->shouldReceive('updateOrder')->andReturn($expectedResult);

        $response = new OrderController($this->orderServiceMock);
        $result = $response->updateOrder($request, $id);
        $result = (array) $result->getData();
        $this->assertArrayHasKey('error', $result);
    }

    public function test_can_update_order_incorrect_param() {

        $id = 2;
        $params = [
            "_method"=> "put",
            "status" => "TAKEN"
        ];

        $expectedResult = [
            'error' => 'Request parameter incorrect or missing', 'httpCode' => 400
        ];

        $request = new Request($params);

        $this->orderServiceMock->shouldReceive('updateOrder')->andReturn($expectedResult);

        $response = new OrderController($this->orderServiceMock);
        $result = $response->updateOrder($request, $id);
        $result = (array) $result->getData();
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('httpCode', $result);
    }

    public function tearDown():void
    {
        parent::tearDown();

        \Mockery::close();
    }
}
