<?php

namespace App\Http\Controllers;

use App\Http\Services\OrderService;
use App\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationData;
use Symfony\Component\Console\Input\Input;

class OrderController extends Controller {

    protected $orderRepo;
    public function __construct(OrderService $orderService)
    {
        $this->orderRepo = $orderService;
    }

    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="L5 OpenApi",
     *      description="L5 Swagger OpenApi description",
     *      @OA\Contact(
     *          email="darius@matulionis.lt"
     *      ),
     *     @OA\License(
     *         name="Apache 2.0",
     *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *     )
     * )
     */

    /**
     * @OA\Get(
     *      path="/task1/public/orders?page={page}&limit={limit}",
     *      operationId="getOrdersList",
     *      tags={"List Orders"},
     *      summary="Get list of orders",
     *      description="Returns list of orders",
     *     @OA\Parameter(
     *         description="Page Number",
     *         in="query",
     *         name="page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Limit Number",
     *         in="query",
     *         name="limit",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns list of projects
     */

    public function getOrders(Request $request){

        $limit = $request['limit'];
        $page = $request['page'];

        $orders = $this->orderRepo->getOrders($page,$limit);
        $orderList = [];

        if(count($orders) == 0) {
            return response()->json($orderList, 200);
        }
        if(isset($orders['error'])){
            return response()->json($orders, 400);
        }

        foreach ($orders as $order){
            $toReturn = [
                'id' => $order['id'],
                'distance' => (int)$order['distance'],
                'status' => $order['status']
            ];
            array_push($orderList,$toReturn);
        }
        return response()->json($orderList, 200);
    }


    /**
     * @OA\Post(
     *     path="/task1/public/order",
     *     tags={"Create Order"},
     *     operationId="addOrder",
     *     summary="Add a new order to the store",
     *     description="",
     *     @OA\RequestBody(
     *         description="Order object that needs to be added to the store",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     type="array",
     *                     description="Origin Coordinates",
     *                     property="origin",
     *                     @OA\Items(
     *                         type="string"
     *                     ),
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     type="array",
     *                     description="Destination Coordinates",
     *                     property="destination",
     *                     @OA\Items(
     *                         type="string"
     *                     ),
     *                     @OA\Items(
     *                         type="string"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order created successfully",
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"petstore_auth":{"write:orders", "read:orders"}}}
     * )
     */

    public function createOrder(Request $request){

        $order = $this->orderRepo->createOrder($request->input());
        return response()->json($order);
    }

    /**
     * @OA\Put(
     *     path="/task1/public/order/{orderId}",
     *     tags={"Order Update"},
     *     operationId="updatePet",
     *     summary="Update an existing order",
     *     description="",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Order object to be updated",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     type="string",
     *                     description="Status",
     *                     property="status"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="Order id to update",
     *         in="path",
     *         name="orderId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     security={{"orderstore_auth":{"write:orders", "read:orders"}}}
     * )
     */

    public function updateOrder(Request $request, $id)
    {
        $order = $this->orderRepo->updateOrder($id, $request->input());
        return response()->json($order);
    }

}
