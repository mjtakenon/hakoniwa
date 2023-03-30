<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Models\Island;
use App\Models\User;

class IndexController extends Controller
{
    public function get()
    {
        return view('pages.register');
    }

    public function post()
    {
        $validator = \Validator::make(\Request::all(), [
            'island_name' => 'string|required|max:32',
            'owner_name' => 'string|required|max:32'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

        $validated = $validator->safe()->collect();

//        \DB::transaction(function ($validated) {
            $island = new Island();
            $island->name = $validated->get('island_name');
            $island->owner_name = $validated->get('owner_name');
            $island->save();
//        });


        $validated->get('owner_name');

        return response()->json();
    }
}
