<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ProductsCollection;
use App\Product;
use App\ShoppingCart;

class ProductController extends Controller
{
    public function __construct(){
      $this->middleware('auth', ['except' => ['index','show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sessionName = 'shopping_cart_id';
        $scid = $request->session()->get($sessionName);
        $shopping_cart = ShoppingCart::findOrCreateById($scid);
        $request->session()->put($sessionName, $shopping_cart->id);

        $products = Product::paginate(15);
        if($request->wantsJson()){
          return new ProductsCollection($products);
        }
        return view('products.index', ['products' => $products, 'shopping_cart' => $shopping_cart]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Product;
        return view('products.create',['product' => $product]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $options = [
          'title' => $request->title,
          'description' => $request->description,
          'price' => $request->price
        ];

        if(Product::create($options)){
          return redirect('/');
        } else {
          return view('products.create');
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
        $product = Product::find($id);
        return view('products.show',['product' => $product]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        return view('products.edit',['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->title = $request->title;
        $product->price = $request->price;
        $product->description = $request->description;
        if($product->save()){
          return redirect('/');
        } else {
          return view('product.edit',['product' => $product]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::destroy($id);
        return redirect('/products');
    }
}
