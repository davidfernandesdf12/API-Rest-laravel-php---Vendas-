<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Seller;
use PHPUnit\Framework\Exception;
use App\API\ApiError;

class SellerController extends Controller
{
    private $seller;

    public function __construct(Seller $seller)
    {
        $this->seller = $seller;
    }
    
    public function index()
    {
        $data = ['data' => $this->seller->paginate(10)];
        return response()->json($data);
    }

    public function show($id)
    {
        $seller = $this->seller->find($id);

        if(!$seller)
            return response()->json(ApiError::erroMessage('Vendedor não encontrado!', 4040), 404);

        $data = ['data' => $seller];
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try
        {
            $sellerData = $request->all();
            $this->seller->create($sellerData);
            $return = ['data' => ['msg' => 'vendedor criado com sucesso!']];
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
            $sellerData = $request->all();
            $seller = $this->seller->find($id);
            $seller->update($sellerData);

            $return = ['data' => ['msg' => 'vendedor atualizado com sucesso!']];
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

    public function delete(Seller $id)
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
