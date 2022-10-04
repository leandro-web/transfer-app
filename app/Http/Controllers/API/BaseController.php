<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

use App\Models\Balance;
use App\Models\Transfer;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function newBalance($input)
    {       

        $balance_payer = Balance::where('user_id', $input['payer'])->first();;
        $balance_payee = Balance::where('user_id', $input['payee'])->first();;
       
        $new_balance_payer = $balance_payer['balance'] - $input['value'];
        $new_balance_payee = $balance_payee['balance'] + $input['value'];

    	$balance_payer->balance = $new_balance_payer;
        $balance_payer->save();

        $balance_payee->balance = $new_balance_payee;
        $balance_payee->save();

        return;
    }

    public function checkBank()
    {   
        $url = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        $json = file_get_contents($url);
        $return = json_decode($json);
        return $return->message;
    }

    public function sendMail($id)
    {   
        $url = 'http://o4d9z.mocklab.io/notify';
        $json = file_get_contents($url);
        $return = json_decode($json);

        if($return->message == "Success"){
            $transfer = Transfer::find($id);
            $transfer->status = 1;
            $transfer->save();
        }
        return;
    }
}