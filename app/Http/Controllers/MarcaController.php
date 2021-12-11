<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return Marca::all();
        $marca = $this->marca->all();
        return response()->json($marca, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());
        $image = $request->file('imagem');
        $img_urn = $image->store('imagens', 's3'); // pasta onde salvar, segundo parâmetro diz onde tipo 's3, local, public' sendo opcional;
        $marca = $this->marca->create(
            [
                'nome' => $request->nome,
                'imagem' => env('AWS_URL') . $img_urn,
            ]
        );

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'NotFound'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'NotFound'], 404);
        }

        if ($request->method() === 'PATCH') {

            $rules = array();

            foreach ($marca->rules() as $input => $rule) {
                if (array_key_exists($input, $request->all())) {
                    $rules[$input] = $rule;
                }
            }
            $request->validate($rules, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }

        if ($request->file('imagem')) {
            Storage::disk('s3')->delete($marca->imagem);
        }

        $image = $request->file('imagem');
        $img_urn = $image->store('imagens', 's3'); // pasta onde salvar, segundo parâmetro diz onde tipo 's3, local, public' sendo opcional;

        $marca->update([
            'nome' => $request->nome,
            'imagem' => env('AWS_URL') . $img_urn,
        ]);

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'NotFound'], 404);
        }

        Storage::disk('s3')->delete($marca->imagem);

        $marca->delete();

        return response()->json(['msg' => 'sucesso'], 200);
    }
}
