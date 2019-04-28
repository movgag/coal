<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SiteController extends Controller
{

    public function index()
    {
        $data = array();
        $data['information'] = Storage::disk('local')->exists('information.json') ? json_decode(Storage::disk('local')->get('information.json')) : [];

        $this->array_sort_by_column($data['information'], 'date');
        return view('welcome',compact('data'));
    }

    public function save(Request $request)
    {
        if ($request->ajax()) {
            $input = $request->except(['_token']);
            $vaidator = Validator::make($input,[
                'name' => 'required|max:255',
                'in_stock' => 'required|digits_between:1,20',
                'price' => 'required|digits_between:1,20',
            ]);
            if ($vaidator->fails()) {
                return response()->json($vaidator->errors(),422);
            }

            $information = Storage::disk('local')->exists('information.json') ? json_decode(Storage::disk('local')->get('information.json')) : [];

            $input['date'] = Carbon::now()->format('Y-m-d H:i:s');

            array_push($information,$input);

            Storage::disk('local')->put('information.json', json_encode($information));

            return response()->json(['message' => 'success', 'data' => $input]);
        }
    }

    // resource https://stackoverflow.com/questions/37567751/laravel-sort-an-array-by-date, // modified
    function array_sort_by_column(&$array, $column, $direction = SORT_DESC) {
        $reference_array = array();

        foreach($array as $key => $row) {
            $row->total = $row->price * $row->in_stock; // calculate total value for each row
            $reference_array[$key] = $row->{$column};
        }

        array_multisort($reference_array, $direction, $array);
    }
}
