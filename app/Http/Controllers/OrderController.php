<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoveOrderToCasherRequest;
use App\Http\Resources\OrderResource;
use App\HttpResponse\CustomResponse;
use App\Models\Meal;
use App\Models\Notification;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\OrderItem;
use App\Models\Table;
use App\SecurityChecker\Checker;
use App\Types\NotificationType;
use App\Types\OrderStates;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use CustomResponse;
    use Checker;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $orders = Order::all();

        return OrderResource::collection($orders);
    }

    public function kitchenOrders () {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $orders = Order::where('order_state' , OrderStates::KITCHEN_ORDER)->get();
        return OrderResource::collection($orders);
    }
    public function runnerOrders () {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $orders = Order::where('order_state' , OrderStates::RUNNER_ORDER)->get();
        return OrderResource::collection($orders);
    }
    public function casherOrders () {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $orders = Order::where('order_state' , OrderStates::CASHER_ORDER)->get();
        return OrderResource::collection($orders);
    }


    public function store(StoreOrderRequest $request)
    {
        if ($this->isExtraFoundInBody(['table_id' , 'order_items'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $request->validated($request->all());

        $order = Order::create([
            'table_id' => $request->table_id
        ]);

        $table = Table::where('id' , $request->table_id)->first();
        $totalItem = 0;
        foreach ($request->order_items as $order_item){
            $meal = Meal::where('id' , $order_item['meal_id'])->first();
            // calc total price of this order ite,
            $totalItem = $order_item['quantity'] * $meal->price;
            $orderItemData = array_merge($order_item , ['order_id' => $order->id , 'total' => $totalItem]);
            $orderItem = OrderItem::create($orderItemData);
            // update total price of sub order
            $order->update([
                'total' => $order->total + $totalItem
            ]);
        }
        $notification = Notification::create([
           'notification' => 'new order added for table number ' . $table->table_number,
            'type' => NotificationType::NEWORDER ,
            'order_id' => $order->id,
        ]);
        return OrderResource::make($order);
    }

    public function show(Order $order)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        return OrderResource::make($order);
    }

    public function destroy(Order $order)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }

        $notification = Notification::create([
            'notification' => 'order number ' . $order->id . ' deleted from system ',
            'type' => NotificationType::DELETEORDER,
            'order_id' => $order->id,
        ]);
        $order->delete();
        return $this->success(null , 'order deleted successfully');
    }

    public function moveToRunner (Order $order) {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $order->update([
            'order_state' => OrderStates::RUNNER_ORDER
        ]);

        $table = Table::where('id' , $order->table_id)->first();
        $notification = Notification::create([
            'notification' => 'order number ' . $order->id . ' belongs to table number ' . $table->id . 'came to runner',
            'type' => NotificationType::TORUNNER,
            'order_id' => $order->id,
        ]);

        return $this->success($order , 'order updated successfully');
    }

    public function moveToCasher(MoveOrderToCasherRequest $request ,Order $order ){
        if ($this->isExtraFoundInBody(['receipt_id'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $request->validated($request->all());
        $order->update([
            'order_state' => OrderStates::CASHER_ORDER,
            'receipt_id' => $request->receipt_id
        ]);
        $table = Table::where('id' , $order->table_id)->first();
        $notification = Notification::create([
            'notification' => 'order number ' . $order->id . ' belongs to table number ' . $table->id . 'came to casher',
            'type' => NotificationType::TOCASHER,
            'order_id' => $order->id,
        ]);

        return $this->success($order , 'order updated successfully');
    }

}
