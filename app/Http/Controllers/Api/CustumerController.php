<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Custumer;
use PHPUnit\Framework\Exception;
use App\API\ApiError;

class CustumerController extends Controller
{
    private $custumer;

    public function __construct(Custumer $custumer)
    {
        $this->custumer = $custumer;
    }
    
    public function index()
    {
        $data = ['data' => $this->custumer->paginate(10)];
        return response()->json($data);
    }

    public function show($id)
    {
        $custumer = $this->custumer->find($id);

        if(!$custumer)
            return response()->json(ApiError::erroMessage('Cliente não encontrado!', 4040), 404);

        $data = ['data' => $custumer];
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try
        {
            $custumerData = $request->all();
            $this->custumer->create($custumerData);
            $return = ['data' => ['msg' => 'Cliente criado com sucesso!']];
            return response()->json($return, 201);

        }
        catch(\Exception $ex)
        {
            if(config('app.debug'))
            {
                return response()->json(ApiError::erroMessage($ex->getMessage(), 1010, 500));
            }

            return response()->json(ApiError::ErroMessage('Houve um erro ao realizar operação de salvar', 1010, 500));
        }
       
    }

    public function update(Request $request, $id)
    {
         try
        {
            $custumerData = $request->all();
            $custumer = $this->custumer->find($id);
            $custumer->update($custumerData);

            $return = ['data' => ['msg' => 'Cliente atualizado com sucesso!']];
            return response()->json($return, 201);

        }
        catch(\Exception $ex)
        {
            if(config('app.debug'))
            {
                return response()->json(ApiError::erroMessage($ex->getMessage(), 1010, 500));
            }

            return response()->json(ApiError::ErroMessage('Houve um erro ao realizar operação de salvar', 1010, 500));
        }
    }

    public function delete(Custumer $id)
    {
        try
        {
            $id->delete();
            return response()->json(['data' => ['msg' => 'Cliente ' . $id->name . ' removido com sucesso!']], 200);
        }
        catch(\Exception $ex)
        {
            if(config('app.debug'))
            {
                return response()->json(ApiError::erroMessage($ex->getMessage(), 1012, 500));
            }

            return response()->json(ApiError::ErroMessage('Houve um erro ao realizar operação', 1012, 500));
        }
    }
}
