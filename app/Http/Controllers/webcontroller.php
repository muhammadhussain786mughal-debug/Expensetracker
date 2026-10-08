<?php

namespace App\Http\Controllers;

use App\Models\expense;
use App\Models\reciept;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class webcontroller extends Controller
{
    public function reciept_get()
    {
        return view("reciept");
    }

    public function reciept_post(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->store('reciepts', 'public');

        $reciept = reciept::create([
            'image' => $path,
        ]);

        $imagepath = storage_path('app/public/' . $reciept->image);

        if (!file_exists($imagepath)) {
            return back()->with('error', 'Receipt image not found.');
        }

        $imageContent = file_get_contents($imagepath);
        $base64img = base64_encode($imageContent);

        $mimeType = mime_content_type($imagepath);

        $imageDataUrl = 'data:' . $mimeType . ';base64,' . $base64img;

        $response = Http::withToken(config('services.groq.key'))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'qwen/qwen3.8-27b',
                'max_tokens' => 500,

                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyze this receipt image and return ONLY valid JSON. Do not include explanations, markdown, code fences, or extra text.

Use exactly this structure:

{
    "store_name": null,
    "date": yyyy-mm-dd,
    "time": null,
    "receipt_no": null,
    "items": [
        {
            "name": null,
            "quantity": null,
            "price": null,
            "total": null
        }
    ],
    "subtotal": null,
    "tax": null,
    "total": null
}

Use null for values that are not visible. Do not guess missing values.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => $imageDataUrl,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        // 8. Check API response
        if ($response->failed()) {
            dd($response->json());
        }

        // 9. Get AI-generated JSON string
        $content = $response->json('choices.0.message.content');

        // 10. Convert JSON string into PHP array
        $data = json_decode($content, true);

        if (!is_array($data)) {
            dd([
                'error' => 'AI did not return valid JSON',
                'content' => $content,
            ]);
        }

       $curexp=expense::create([
            'reciept_id' => $reciept->id,
            'Mart_name' => $data['store_name'],
            'total_amount' => $data['total'],
            'Date_Expense' => $data['date'],
        ]);
       
        return view("/reciept",compact('curexp'));
        // dd($data);
    }
}
