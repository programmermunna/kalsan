<?php

namespace App\Http\Controllers;

use App\Models\ProductService;
use App\Models\Purchase;
use App\Models\Utility;
use App\Models\warehouse;
use App\Models\WarehouseProduct;
use App\Models\WarehouseTransfer;
use DB;
use Illuminate\Http\Request;

class WarehouseTransferController extends Controller
{

    public function index()
    {
        $warehouse_transfers = WarehouseTransfer::where('created_by', '=', \Auth::user()->creatorId())->with(['product','fromWarehouse'])->get();
        return view('warehouse-transfer.index',compact('warehouse_transfers'));
    }

    public function create()
    {
        $from_warehouses      = warehouse::where('created_by', '=', \Auth::user()->creatorId())->get();
        $to_warehouses     = warehouse::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $to_warehouses->prepend('Select Warehouse', '');
        $ware_pro= WarehouseProduct::join('product_services', 'warehouse_products.product_id', '=', 'product_services.id')
                                ->pluck('name','product_id');
        $ware_pro->prepend('Select products', '');

        return view('warehouse-transfer.create',compact('from_warehouses','to_warehouses','ware_pro'));

    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create warehouse'))
        {
            $validator = \Validator::make(
                $request->all(), [
                    'from_warehouse' => 'required',
                    'to_warehouse' => 'required',
                    'product_id' => 'required',
                    'quantity' => 'required',
                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $fromWarehouse    = WarehouseProduct::where('warehouse_id',$request->from_warehouse)
                                ->where('product_id',$request->product_id)->first();

            if($request->quantity <= $fromWarehouse->quantity)
            {
                $warehouse_transfer                  = new WarehouseTransfer();
                $warehouse_transfer->from_warehouse  = $request->from_warehouse;
                $warehouse_transfer->to_warehouse    = $request->to_warehouse;
                $warehouse_transfer->product_id      = $request->product_id;
                $warehouse_transfer->quantity        = $request->quantity;
                $warehouse_transfer->date            = $request->date;
                $warehouse_transfer->created_by      = \Auth::user()->creatorId();
                $warehouse_transfer->save();
            }
            else
            {
                return redirect()->route('warehouse-transfer.index')->with('error', __('Product out of stock!.'));
            }
            Utility::warehouse_transfer_qty($request->from_warehouse,$request->to_warehouse,$request->product_id,$request->quantity);

            return redirect()->route('warehouse-transfer.index')->with('success', __('Warehouse Transfer successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show()
    {
        return redirect()->route('warehouse-transfer.index');

    }

    public function destroy(WarehouseTransfer $warehouseTransfer)
    {
        if(\Auth::user()->can('delete warehouse'))
        {
            if($warehouseTransfer->created_by == \Auth::user()->creatorId())
            {
                Utility::warehouse_transfer_qty($warehouseTransfer->to_warehouse,$warehouseTransfer->from_warehouse,$warehouseTransfer->product_id,$warehouseTransfer->quantity);

                $warehouseTransfer->delete();

                return redirect()->route('warehouse-transfer.index')->with('success', __('Warehouse Transfer successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getproduct(Request $request)
    {
        if($request->warehouse_id == 0)
        {
            $ware_products= WarehouseProduct::join('product_services', 'warehouse_products.product_id', '=', 'product_services.id')
                ->get()
                ->pluck('name', 'product_id')->toArray();
            $to_warehouses     = warehouse::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        }
        else
        {
            $ware_products= WarehouseProduct::join('product_services', 'warehouse_products.product_id', '=', 'product_services.id')
                ->where('warehouse_id', $request->warehouse_id)
                ->get()
                ->pluck('name', 'product_id')->toArray();
            $to_warehouses     = warehouse::where('id','!=',$request->warehouse_id)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        }
        $result = [];
        $result['ware_products'] = $ware_products;
        $result['to_warehouses'] = $to_warehouses;
        return response()->json($result);
    }

    public function getquantity(Request $request)
    {
        if($request->product_id == 0)
        {
            $pro_qty = WarehouseProduct::where('created_by', '=', \Auth::user()->creatorId())
                ->get()->pluck('quantity', 'product_id')->toArray();
        }
        else
        {
            $pro_qty = WarehouseProduct::where('created_by', '=', \Auth::user()->creatorId())
                        ->where('product_id', $request->product_id)
                        ->get()->pluck('quantity');
    
        }
        return response()->json($pro_qty);
    }
}
