<?php

namespace App\Http\Controllers;

use App\Http\Resources\SongResource;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SongsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|Response
     */
    public function index(): Response|JsonResponse
    {
        $songs = Song::all();
        return SongResource::collection($songs)->response();
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return JsonResponse|StreamedResponse
     * @throws ValidationException
     */
    public function show(Request $request): StreamedResponse|JsonResponse
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
            Log::warning("[SongController] 'file_name' query parameter is not set.");
            $mapping_fnc = fn($elem) => $elem[0];
            $message = implode(' ', array_map($mapping_fnc, $validator->errors()->messages()));
            return response()->json([
                'error' => "Required 'file_name' query parameter is not set",
                'message' => $message
            ], 400);
        }

        $file_name = $validator->validated()['file_name'];
        Log::info("[SongController] Requesting to download file with name '" . $file_name . "'");

        $file_path = 'songs/' . $file_name;
        if (!Storage::exists($file_path) || $file_name == ".gitkeep") {
            Log::warning("[SongController] The file does not exist");
            return response()->json(['error' => "File not found"], status: 404);
        }

        Log::info('[SongController] The file does exist');
        return Storage::download($file_path);
    }
}
