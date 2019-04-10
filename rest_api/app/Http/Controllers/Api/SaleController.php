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
        $sales = $this->sale->all();
        
        //collection para retornar json estruturado com produtos
        $data = collect([]);
        $dataProducts = collect([]);

        for($i=0; $i < count($sales); $i++){    
            $products = $this->saleHasProduct->all('id_sale','id_product')->where('id_sale', $sales[$i]->id);

        for($p = 0; $p < count($products); $p++)
        {
            $dataProducts->add($products[$p]->id_product);
        }
        
        $data->add(['id'=>$sales[$i]->id, 'id_custumer' => $sales[$i]->id_custumer,'id_seller'=>$sales[$i]->id_seller, 'data' => $sales[$i]->created_at->format('d/m/Y h:i:s'), 'products' => $dataProducts]);
        $dataProducts = collect([]);
        }

        return response()->json(['data' => $data]);          
    }

    public function show($id)
    {
        $sale = $this->sale->find($id);

        $products = $this->saleHasProduct->all('id_sale','id_product')->where('id_sale', $sale->id);

        //collection para retornar json estruturado com produtos
        $data = collect([]);
        $dataProducts = collect([]);

        for($p = 0; $p < count($products); $p++)
        {
            $dataProducts->add($products[$p]->id_product);
        }
        
        if(!$sale)
            return response()->json(ApiError::erroMessage('Venda não encontrado!', 4040), 404);

        $data->add(['id'=>$sale->id, 'id_custumer' => $sale->id_custumer,'id_seller'=>$sale->id_seller, 'data' => $sale->created_at->format('d/m/Y h:i:s'), 'products' => $dataProducts]);

        return response()->json(['data' => $data]);          
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
                    $this->saleHasProduct->create(['id_sale' => $sale->id, 'id_product' => $produto[$i]]);
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
            //update da venda
            $saleData = $request->all('id_product');;
            $sale = $this->sale->find($id);
            // $sale->update($saleData);

            //update dos produtos relacionados a venda
            // $saleHasProducts = $request->all()->where('id_sale', $sale->id_sale);
            // $products = $this->saleHasProduct->contains()
            $listProducts = collect([]);

            $data = $this->saleHasProduct->all('id_product', 'id_sale')->where('id_sale', $id);

            for($i = 0; $i<count($data); $i++){
                $listProducts->add($data[$i]->id_product);
            }

            return response()->json(['products' => $saleData]);
            // $return = ['data' => ['msg' => 'Venda atualizado com sucesso!']];
            // return response()->json($return, 201);

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
