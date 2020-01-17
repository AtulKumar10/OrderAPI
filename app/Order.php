<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'origin_lat',
        'origin_long',
        'destination_lat',
        'destination_long',
        'distance'
    ];

    public function getOrders($page, $limit){
        return Order::offset(($page - 1) * $limit)->limit($limit)->get();
    }
    public function createOrder($data) : Order
    {
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
        return $order;
    }

    public function findOrder($id) : Order
    {
        return Order::find($id);
    }

    public function updateOrder($data) : Order
    {
        Order::where('id',$data['id'])->update($data);
        return Order::find($data['id']);
    }

    private function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL&key=AIzaSyAsrBU8y2qXf7pwMFIeD-B8p3TFysVYzMY";

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
