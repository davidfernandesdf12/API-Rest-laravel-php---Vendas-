<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sale;
use App\SaleHasProducts;
use PHPUnit\Framework\Exception;
use App\API\ApiError;

class SaleController extends Controller
{
    private $sale;
    private $saleHasProduct;

    public function __construct(Sale $sale, SaleHasProducts $saleHasProduct)
    {
        $this->sale = $sale;
        $this->saleHasProduct = $saleHasProduct;
    }

    public function index()
    {
        $data = ['data' => $this->sale->paginate(10)];
        return response()->json($data);
    }

    public function show($id)
    {
        $sale = $this->sale->find($id);

        if(!$sale)
            return response()->json(ApiError::erroMessage('Venda não encontrado!', 4040), 404);

        $data = ['data' => $sale];
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try
        {
            // parâmetros referente a venda
            $saleData = $request->only('id_custumer', 'id_seller');

            $sale = $this->sale->create($saleData);

            //parâmetros referente aos produtos da venda
            $saleHasProductsData = $request->only('id_product');

            foreach($saleHasProductsData as $produto)
            {
                for($i = 0; $i<count($produto); $i++){
                    $this->saleHasProduct->create(['id_product' => $produto[$i], 'id_sale' => $sale->id]);
                }
            }

            $return = ['data' => ['msg' => 'Venda registrada com sucesso!']];
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
            $sale = $this->sale->find($id);
            $sale->update($saleData);

            $return = ['data' => ['msg' => 'Venda atualizado com sucesso!']];
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

    public function delete(Sale $id)
    {
        try
        {
            $id->delete();
            return response()->json(['data' => ['msg' => 'Venda ' . $id->description . ' removido com sucesso!']], 200);
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
