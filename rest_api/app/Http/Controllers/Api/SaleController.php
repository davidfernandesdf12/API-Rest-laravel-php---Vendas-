<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sale;
use App\SaleHasProducts;
use PHPUnit\Framework\Exception;
use App\API\ApiError;
use App\Product;
use App\Custumer;
use App\Seller;


class SaleController extends Controller
{
    private $sale;
    private $saleHasProduct;
    private $custumer;
    private $seller;
    private $product;

    public function __construct(Sale $sale, SaleHasProducts $saleHasProduct,Custumer $custumer, Seller $seller, Product $products)
    {
        $this->sale = $sale;
        $this->saleHasProduct = $saleHasProduct;
        $this->custumer = $custumer;
        $this->seller = $seller;
        $this->product = $products;
    }

    public function index()
    {
        $sales = $this->sale->all();         
        
        //collection para retornar json estruturado com produtos
        $data = collect([]);
        $dataProducts = collect([]);
        $ReturnProducts = collect([]);

        
        for($i=0; $i < count($sales); $i++){    
            $products = $this->saleHasProduct->all('id_sale','id_product')->where('id_sale', $sales[$i]->id);

        foreach ($products as $key => $value) {
            $dataProducts->add($value);
        }

        for($p = 0; $p < count($dataProducts); $p++)
        {
            $ReturnProducts->add($this->product->find($dataProducts[$p]->id_product)->only('id','description', 'price'));
            
        }
        
        $data->add(['id'=>$sales[$i]->id, 'Cliente' =>  $this->custumer->find($sales[$i]->id_custumer)->only('id','name'), 
        'Vendedor' => $this->seller->find($sales[$i]->id_seller)->only('id','name'), 'data' => $sales[$i]->created_at->format('d/m/Y h:i:s'),
        'Produtos' => $ReturnProducts, 'Total' => number_format($ReturnProducts->sum('price'), 2, ',', '')]);
        $dataProducts = collect([]);
        $ReturnProducts = collect([]);
        
        }
        
        return response()->json(['data' =>  $data]);  
        
    }

    public function show($id)
    {
        //collection para retornar json estruturado com produtos
        $data = collect([]);
        $dataProducts = collect([]);
        $ReturnProducts = collect([]);

        $sale = $this->sale->find($id);

        $products = $this->saleHasProduct->all('id_sale','id_product')->where('id_sale', $id);

        foreach ($products as $key => $value) {
            $dataProducts->add($value);
        }
        
        for($p = 0; $p < count($dataProducts); $p++)
        {
            $ReturnProducts->add($this->product->find($dataProducts[$p]->id_product)->only('id','description', 'price'));
        }
        
        if(!$sale)
            return response()->json(ApiError::erroMessage('Venda não encontrado!', 4040), 404);

         $data->add(['id'=>$sale->id, 'Cliente' =>  $this->custumer->find($sale->id_custumer)->only('id','name'), 
        'Vendedor' => $this->seller->find($sale->id_seller)->only('id','name'), 'data' => $sale->created_at->format('d/m/Y h:i:s'),
        'Produtos' => $ReturnProducts, 'Total' => number_format($ReturnProducts->sum('price'), 2, ',', '')]);

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

            
            foreach($saleHasProductsData as $key => $value )
            {
              for($i = 0; $i < count($value); $i++){
                
               $this->saleHasProduct->create(['id_sale' => $sale->id, 'id_product' => $value[$i]]);

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
            $saleData = $request->all('id_custumer', 'id_seller');
            
            
            $sale = $this->sale->find($id);

            if(!$sale)
            {
                return response()->json(ApiError::erroMessage('Produto não encontrado!', 4040), 404);
            }
            else
            {
                if(!$sale)
                return response()->json(ApiError::erroMessage('Venda não encontrado!', 4040), 404);
        

                if($saleData)
                    $sale->update($saleData);


                $saleProdutos = $request->all('id_product');

                if($saleProdutos)
                {
                    //removendo produtos atuais
                    $this->saleHasProduct->where('id_sale', $id)->delete();

                    //inserindo produtos 
                    foreach($saleProdutos as $key => $value )
                    {
                    for($i = 0; $i < count($value); $i++){
                        
                    $this->saleHasProduct->create(['id_sale' => $id, 'id_product' => $value[$i]]);

                    }

                    }
                }
                $return = ['data' => ['msg' => 'Venda alterada com sucesso.']];
                return response()->json($return, 201);
            }


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
            
            //removendo produtos da venda relacionada
            $data = $this->saleHasProduct->all()->where('id_sale', $id->id);

            if($data != null){
                for ($i=0; $i < count($data) ; $i++) { 
                    $data[$i]->delete();
                }
            }
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

    public function sale_sellers(){
        try
        {
            $sellers = $this->seller->all();
            $data = collect([]);

          for($i = 0; $i<count($sellers); $i++)
          {
            $data->add(['vendedor'=>$sellers[$i]->name, 'Quantidade de Vendas' => $this->sale->where('id_seller', $sellers[$i]->id)->count()]);
          }

          return $data;
            
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
