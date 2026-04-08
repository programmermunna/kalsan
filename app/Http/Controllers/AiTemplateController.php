<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use Orhanerday\OpenAi\OpenAi;
use App\Models\Utility;
use Illuminate\Support\Facades\DB;

class AiTemplateController extends Controller
{
    public function create($moduleName)
    {
        $templateName = Template::where('module',$moduleName)->get();
        return view('template.generate_ai', compact('templateName'));
    }

    public function getKeywords(Request $request , $id){
        $template = Template::find($id);
        $field_data=json_decode($template->field_json);
        $html ="";
        foreach ($field_data->field as  $value) {
            $html.='<div class="form-group col-md-12">
                     <label class="form-label ">'.$value->label.'</label>';
            if($value->field_type=="text_box")
            {

                $html.='<input type="text" class="form-control" name="'.$value->field_name.'" value="" placeholder="'.$value->placeholder.'" required">';
            }
            if($value->field_type=="textarea")
            {
                $html.='<textarea type="text" rows=3 class="form-control " id="description" name="'.$value->field_name.'" placeholder="'.$value->placeholder.'" required></textarea>';
            }
            $html.='</div>';
        }
        return response()->json(
            [
                'success' => true,
                'tone'=>$template->is_tone,
                'template' => $html,
            ]
        );
    }
    public function AiGenerate(Request $request)
    {
        $data['status'] = 'Error';
        $data['message'] = $response['error']['message'];
        return $data;
        
        if ($request->ajax())
        {

            $post=$request->all();

            unset($post['_token'],$post['template_name'],$post['tone'],$post['ai_creativity'],$post['num_of_result'],$post['result_length']);
            $data=array();
            $key_data = DB::table('settings')->where('name','chat_gpt_key')->first();

            if($key_data)
            {

                $open_ai = new OpenAi($key_data->value);
            }
            else
            {
                $data['status'] = 'Error';
                $data['message'] = __('Please set proper configuration for Api Key');
                return $data;
            }

            $prompt = '';
            $model = '';
            $text = '';
            $ai_tokens = '';
            $counter = 1;


            $template = Template::where('id', $request->template_name)->first();

            if ($request->template_name) {


                $required_field=array();
                $data_field=json_decode($template->field_json);
                foreach ($data_field->field as  $val)
                {
                    request()->validate([$val->field_name => 'required|string']);
                }

                $prompt=$template->prompt;
                foreach ($data_field->field as  $field)
                {

                    $text_rep="##".$field->field_name."##";
                    if(strpos($prompt,$text_rep) !== false)
                    {
                        $field->value=$post[$field->field_name];
                        $prompt=str_replace($text_rep,$post[$field->field_name],$prompt);
                    }
                    if($template->is_tone==1)
                    {
                        $tone=$request->tone;
                        $param="##tone_language##";
                        $prompt=str_replace($param,$tone,$prompt);
                    }
                }
            }
            $lang_text = "Provide response in " . $request->language . " language.\n\n ";
            $ai_tokens = (int)$request->result_length;

            $max_results = (int)$request->num_of_result;
            $ai_creativity = (float)$request->ai_creativity;
            $modelName = Utility::getValByName('chat_gpt_model') ?? '';

            $isChatModel = isset($modelName) && $modelName == 'gpt-3.5-turbo-instruct';
            if ($isChatModel) {
                $complete = $open_ai->completion([
                    'model' => $modelName ? $modelName : '',
                    'prompt' => $prompt.' '.$lang_text,
                    'temperature' => $ai_creativity,
                    'max_tokens' => $ai_tokens,
                    'n' => $max_results
                ]);
            } else {
                $complete = $open_ai->chat([
                    'model' => $modelName ? $modelName : '',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $prompt.' '.$lang_text],
                    ],
                    'temperature' => $ai_creativity,
                    'max_tokens' => $ai_tokens,
                    'n' => $max_results
                ]);
            }

            $response = json_decode($complete , true);
            if (isset($response['choices']))
            {
                if ($isChatModel) {
                    if (count($response['choices']) > 1) {
                        foreach ($response['choices'] as $value) {
                            $text .= $counter . '. ' . ltrim($value['text']) . "\r\n\r\n\r\n";
                            $counter++;
                        }
                    } else {
                        $text = $response['choices'][0]['text'];
                    }
                } else {
                    if (count($response['choices']) > 1) {
                        foreach ($response['choices'] as $value) {
                            $text .= $counter . '. ' . ltrim($value['message']['content']) . "\r\n\r\n\r\n";
                            $counter++;
                        }
                    } else {
                        $text = $response['choices'][0]['message']['content'];
                    }
                }

                $tokens = $response['usage']['completion_tokens'];
                $data= trim($text);
                return $data;

            }
            else
            {
                $data['status'] = 'Error';
                $data['message'] = $response['error']['message'];
                return $data;
            }

        }
    }

    //for grammar check
    public function grammar($moduleName)
    {
        $templateName = Template::where('module',$moduleName)->first();
        return view('template.grammar_ai', compact('templateName'));
    }

    public function grammarProcess(Request $request)
    {

        if ($request->ajax())
        {
            $post=$request->all();

            unset($post['_token'],$post['template_name'],$post['tone'],$post['ai_creativity'],$post['num_of_result'],$post['result_length']);
            $data=array();
            $key_data = DB::table('settings')->where('name','chat_gpt_key')->first();

            if($key_data)
            {
                $open_ai = new OpenAi($key_data->value);
            }
            else
            {
                $data['status'] = 'Error';
                $data['message'] = __('Please set proper configuration for Api Key');
                return $data;
            }

            $counter = 1;
            $prompt = "please correct grammar mistakes and spelling mistakes in this: . $request->description .";
            $is_tone=1;
            $ai_tokens = strlen($request->description);
            $text = '';
            $max_results = 1;
            $ai_creativity = 1.0;
            $modelName = Utility::getValByName('chat_gpt_model') ?? '';

            $isChatModel = isset($modelName) && $modelName == 'gpt-3.5-turbo-instruct';
            if ($isChatModel) {
                $complete = $open_ai->completion([
                    'model' => $modelName ? $modelName : '',
                    'prompt' => $prompt,
                    'temperature' => $ai_creativity,
                    'max_tokens' => $ai_tokens,
                    'n' => $max_results
                ]);
            } else {
                $complete = $open_ai->chat([
                    'model' => $modelName ? $modelName : '',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => $ai_creativity,
                    'max_tokens' => $ai_tokens,
                    'n' => $max_results
                ]);
            }

            $response = json_decode($complete , true);
            if (isset($response['choices']))
            {
                if ($isChatModel) {
                    if (count($response['choices']) > 1) {
                        foreach ($response['choices'] as $value) {
                            $text .= $counter . '. ' . ltrim($value['text']) . "\r\n\r\n\r\n";
                            $counter++;
                        }
                    } else {
                        $text = $response['choices'][0]['text'];
                    }
                } else {
                    if (count($response['choices']) > 1) {
                        foreach ($response['choices'] as $value) {
                            $text .= $counter . '. ' . ltrim($value['message']['content']) . "\r\n\r\n\r\n";
                            $counter++;
                        }
                    } else {
                        $text = $response['choices'][0]['message']['content'];
                    }
                }
                
                $tokens = $response['usage']['completion_tokens'];
                $data= trim($text);
                return $data;
            }
            else
            {
                $data['status'] = 'Error';
                $data['message'] =  $response['error']['message'];
                return $data;
            }

        }
    }
}
