<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\User;
use App\Models\LeadStage;
use App\Models\Source;
use App\Models\Pipeline;
use App\Models\Label;
use App\Models\ProductService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = Lead::where('created_by', \Auth::user()->creatorId())->get();

        foreach($data as $k => $lead)
        {
            unset($lead->id,$lead->order,
            $lead->created_by,$lead->is_active , $lead->is_converted,
            $lead->created_at,$lead->updated_at);

            $user = User::find($lead->user_id);
            $pipeline = Pipeline::find($lead->pipeline_id);
            $stage = LeadStage::find($lead->stage_id);

            $sources = Source::whereIn('id', explode(',', $lead->sources))->get();
            $sourceName = [];
            
            foreach ($sources as $source) {
                $sourceName[] = $source->name;
            }

            $products = ProductService::whereIn('id', explode(',', $lead->products))->get();
            $productName = [];
            
            foreach ($products as $product) {
                $productName[] = $product->name;
            }

            $labels = Label::whereIn('id', explode(',', $lead->products))->get();
            $labelName = [];
            
            foreach ($labels as $label) {
                $labelName[] = $label->name;
            }

            $data[$k]["user_id"] =   !empty($user) ? $user->name : '';
            $data[$k]["pipeline_id"]     = $pipeline->name;
            $data[$k]["stage_id"]     =  $stage->name;
            $data[$k]["sources"]     = implode(',', $sourceName);
            $data[$k]["products"]     = implode(',', $productName);
            $data[$k]["labels"]     = implode(',', $labelName);

        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "Name",
            "Email",
            "Contact",
            "Subject",
            "User",
            "Pipeline",
            "Lead Stage",
            "Lead Sources",
            "Products",
            "Notes",
            "Labels",
            "Date",
        ];
    }
}
