<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Transfer;
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
   
        $validator = Validator::make($input, [
            'payer' => 'required',
            'payee' => 'required',
            'value' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Erro de validação.', $validator->errors());       
        }
   
        $transfer = Transfer::create($input);
   
        return $this->sendResponse(new TransferResource($transfer), 'Transferência realizada.');
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
            'payer' => 'required',
            'payee' => 'required',
            'value' => 'required',
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