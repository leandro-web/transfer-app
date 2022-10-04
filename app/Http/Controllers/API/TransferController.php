<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Balance;
use Validator;
use App\Http\Resources\TransferResource;
   
class TransferController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transfers = Transfer::all();
    
        return $this->sendResponse(TransferResource::collection($transfers), 'Lista de transferências.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $payer = User::find($input['payer']);
        $payee = User::find($input['payee']);

        if($payer['profile_id'] === 1){            
            $validator = Validator::make($input, [
                'payer' => 'required|numeric',
                'payee' => 'required|numeric',
                'value' => 'required|numeric|min:0',
            ]);
       
            if($validator->fails()){
                return $this->sendError('Erro de validação.', $validator->errors());       
            }

            $balance_payer = Balance::find($input['payer'])->balance;

            if($balance_payer >= $input['value']){
                $bank = $this->checkBank();
                if($bank == 'Autorizado'){
                    $transfer = Transfer::create($input);
                    $this->newBalance($input); 
                    $mail = $this->sendMail($transfer->id);                                    
                    return $this->sendResponse(new TransferResource($transfer), 'Transferência realizada.');
                }else{
                    return $this->sendError('Erro de validação.', 'Não autorizado pelo banco'); 
                }
                
            }else{
                return $this->sendError('Erro de validação.', 'Saldo insuficiente');  
            }       
        }else{
            return $this->sendError('Erro de validação.', 'Somente usuários comuns podem realizar transferência');  
        }           
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transfer = Transfer::find($id);
  
        if (is_null($transfer)) {
            return $this->sendError('Transferência não encontrada.');
        }
   
        return $this->sendResponse(new TransferResource($transfer), 'Detalhe da transferência.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transfer $transfer)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'payer' => 'required|numeric',
            'payee' => 'required|numeric',
            'value' => 'required|numeric|min:0',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Erro de validação.', $validator->errors());       
        }
   
        $transfer->payer = $input['payer'];
        $transfer->payee = $input['payee'];
        $transfer->value = $input['value'];
        $transfer->save();
   
        return $this->sendResponse(new TransferResource($transfer), 'Transferência atualizada.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $transfer)
    {
        $transfer->delete();
   
        return $this->sendResponse([], 'Transferência deletada');
    }
}