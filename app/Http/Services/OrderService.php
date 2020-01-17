<?php

namespace App\Http\Services;

use App\Order;

class OrderService {

    public function getOrders($page, $limit){
        if($page<1 || $limit<1){
            return ['error'=>'Page number and limit must be equal to or greater than 1'];
        }
        $order = Order::offset(($page - 1) * $limit)->limit($limit)->get();
        return $order;
    }
    public function createOrder($data)
    {
        $validatedData = true;

        if(empty($data['origin']) || empty($data['destination']) || count($data['origin'])!=2 || count($data['destination'])!=2){
            $validatedData = false;
        }
        if($validatedData) {
            $order = new Order();
            $order->origin_lat = $data['origin'][0];
            $order->origin_long = $data['origin'][1];
            $order->destination_lat = $data['destination'][0];
            $order->destination_long = $data['destination'][1];
            $order->status = 'UNASSIGNED';

            $distanceData = $this->GetDrivingDistance($order->origin_lat, $order->destination_lat, $order->origin_long, $order->destination_long);
            $distance = $distanceData['distance'];

            $order->distance = $distance;
            $order->save();

            $toReturn = [
                'id' => $order['id'],
                'distance' => (int)$order['distance'],
                'status' => $order['status']
            ];
            return $toReturn;
        }
        return ['error'=>'Request parameter incorrect or missing', 'httpCode' => 400];
    }

    public function findOrder($id)
    {
        $order = Order::find($id);
        if (!$order){
            return ['error'=>'Order not found'];
        }
        return $order;
    }

    public function updateOrder($id, $request)
    {
        if(empty($request['status']) || empty($id)){
            return ['error'=>'Request parameter incorrect or missing', 'httpCode' => 400];
        }
        $order = Order::find($id);

        if (!$order){
            return ['error'=>'Order not found'];
        }
        if ($order->status != $request['status']) {
            $order->status = $request['status'];
            $order->save();
            return ['status'=>'SUCCESS'];
        }
        else{
            return ['error'=>'Order already taken'];
        }
    }

    private function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL&key=";

        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);

            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
        }
        catch (\Exception $exception){
            return array('distance' => 0, 'time' => 0);
        }
        //dd($response_a);


        return array('distance' => $dist, 'time' => $time);
    }
}
