<?php

namespace App\Http\Controllers\Frontend\Updates;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\UpdateRecipients;
use Illuminate\Http\Request;
use Response;
use Validator;

class UpdatesController extends Controller
{

    public function store(Request $request)
    {
        $messages = array(
            'email.unique' => trans('UpdatesController.email-unique'),
        );
        $rules = [
            'email' => 'required|email|unique:update_recipients',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json(['error' => $validator->errors()->all()]);
        }
        try {
            $update = new UpdateRecipients();
            $update->email = $request->input('email');
            $update->participate = $request->input('participate');

            // if geolocation fields were passed along then see
            // to that they get saved in the row 
            if ($request->input('geo_lat') && $request->input('geo_long')) {
                $update->geo_lat = $request->input('geo_lat');
                $update->geo_long = $request->input('geo_long');
            }
            
            $update->save();
            return Response::json(['success' => [trans('UpdatesController.store-success')]]);
            // @codeCoverageIgnoreStart
        } catch (\Exception $ex) {
            return Response::json(['error' => [$ex->getMessage()]]);
            // @codeCoverageIgnoreEnd
        }
    }
}