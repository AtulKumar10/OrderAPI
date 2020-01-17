<?php
namespace App\Repositories;

use App\Order;

class OrderRepository
{
    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    public function getOrders(int $page, int $limit)
    {
        return $this->model->getOrders($page,$limit);
    }

    public function createOrder(array $data) : Order
    {
        return $this->model->createOrder($data);
    }

    public function findOrder($id) : Order
    {
        return $this->model->findOrder($id);
    }

    public function updateOrder($data) : Order
    {
        return $this->model->updateOrder($data);
    }
}
