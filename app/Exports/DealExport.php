<?php

namespace App\Exports;

use App\Models\Deal;
use App\Models\User;
use App\Models\Stage;
use App\Models\Source;
use App\Models\Pipeline;
use App\Models\ClientDeal;
use App\Models\UserDeal;
use App\Models\Label;
use App\Models\ProductService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DealExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = Deal::where('created_by', \Auth::user()->creatorId())->get();
        
        foreach($data as $k => $deal)
        {
            $clientsData = ClientDeal::where('deal_id' , $deal->id)->get()->pluck('deal_id','client_id')->toArray();
            $usersData = UserDeal::where('deal_id' , $deal->id)->pluck('deal_id','user_id')->toArray();

            unset($deal->id,$deal->order,
            $deal->created_by,$deal->is_active , $deal->status,
            $deal->created_at,$deal->updated_at);

            $pipeline = Pipeline::find($deal->pipeline_id);
            $stage = Stage::find($deal->stage_id);

            $sources = Source::whereIn('id', explode(',', $deal->sources))->get();
            $sourceName = [];
            
            foreach ($sources as $source) {
                $sourceName[] = $source->name;
            }

            $products = ProductService::whereIn('id', explode(',', $deal->products))->get();
            $productName = [];
            
            foreach ($products as $product) {
                $productName[] = $product->name;
            }

            $labels = Label::whereIn('id', explode(',', $deal->products))->get();
            $labelName = [];
            
            foreach ($labels as $label) {
                $labelName[] = $label->name;
            }

            $users = User::whereIn('id', array_keys($usersData))->get();
            $userName = [];
            
            foreach ($users as $user) {
                $userName[] = $user->name;
            }

            $clients = User::whereIn('id', array_keys($clientsData))->get();
            $clientName = [];
            
            foreach ($clients as $client) {
                $clientName[] = $client->name;
            }
            $deal["client_id"] = '';
            $deal["user_id"] = '';

            $data[$k]["pipeline_id"]     = $pipeline->name;
            $data[$k]["stage_id"]     =  $stage->name;
            $data[$k]["sources"]     = implode(',', $sourceName);
            $data[$k]["products"]     = implode(',', $productName);
            $data[$k]["labels"]     = implode(',', $labelName);
            $data[$k]["client_id"] =   implode(',', $clientName);
            $data[$k]["user_id"] =   implode(',', $userName);
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "Name",
            "Phone",
            "Price",
            "Pipeline",
            "Deal Stage",
            "Deal Sources",
            "Products",
            "Notes",
            "Labels",
            "Clients",
            "Deal User"
        ];
    }
}
