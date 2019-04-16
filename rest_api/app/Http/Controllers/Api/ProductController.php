<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use PHPUnit\Framework\Exception;
use App\API\ApiError;

class ProductController extends Controller
{
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function index()
    {
        $data = ['data' => $this->product->paginate(10)];
        return response()->json($data);
    }

    public function show($id)
    {
        $product = $this->product->find($id);

        if(!$product)
            return response()->json(ApiError::erroMessage('Produto não encontrado!', 4040), 404);

        $data = ['data' => $product];
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try
        {
            $productData = $request->all();
            $this->product->create($productData);
            $return = ['data' => ['msg' => $productData]];
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
            $productData = $request->all();
            $product = $this->product->find($id);
            $product->update($productData);

            $return = ['data' => ['msg' => 'Produto atualizado com sucesso!']];
            return response()->json($return, 201);

        }
        catch(\Exception $ex)
        {
            if(config('app.debug'))
            {
                return response()->json(ApiError::erroMessage($ex->getMessage(), 1011, 500));
            }

            return response()->json(ApiError::ErroMessage('Houve um erro ao realizar operação de atualuzar', 1011, 500));
        }
       
    }

    public function delete(Product $id)
    {
        try
        {
            $id->delete();
            return response()->json(['data' => ['msg' => 'Produto ' . $id->description . ' removido com sucesso!']], 200);
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
