<?php

namespace App\Http\Controllers;

use App\Http\Resources\TrimmedConcertResource;
use App\Models\Concert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConcertsRecordingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $concerts = Concert::has('recordings')->get();
        return TrimmedConcertResource::collection($concerts)->response();
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse|StreamedResponse
     * @throws ValidationException
     */
    public function show(Request $request): JsonResponse|StreamedResponse
    {
        $validator = Validator::make($request->all(), [
            'file_name' => [
                'required',
                'filled',
                function ($attribute, $value, $fail) use ($request) {
                    $is_query_param = $request->query('file_name') == $value;
                    if (!$is_query_param) {
                        $fail('The ' . $attribute . ' is invalid.');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            Log::warning("[ConcertRecordingsController] 'file_name' query parameter is not set.");
            $mapping_func = fn($elem) => $elem[0];
            $message = implode(' ', array_map($mapping_func, $validator->errors()->messages()));
            return response()->json([
                'error' => "Required 'file_name' query parameter is not set",
                'message' => $message
            ], 400);
        }

        $file_name = $validator->validated()['file_name'];
        Log::info("[ConcertRecordingsController] Requesting to download file with name '" . $file_name . "'");

        $file_path = 'recordings/' . $file_name;
        if (!Storage::exists($file_path) || $file_name == ".gitkeep" || $file_name == '.gitignore') {
            Log::warning("[ConcertRecordingsController] The file does not exist");
            return response()->json(['error' => "File not found"], status: 404);
        }

        Log::info('[ConcertRecordingsController] The file does exist');
        return Storage::download($file_path);
    }
}
