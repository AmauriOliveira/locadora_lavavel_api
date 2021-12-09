<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];

    public function rules()
    {
        return [
            'nome' => 'required|unique:marcas,nome,' . $this->id . '|min:3',
            'imagem' => 'required|file|mimes:png,jpeg,jpg,gif',
        ];
    }

    public function feedback()
    {
        return  [
            'required' => 'O campo :attribute é obrigatório.',
            'nome.unique' => 'O nome da marca já existe.',
            'nome.min' => 'O nome deve ter no mínimo três caracteres.',
            'imagem.file' => 'A imagem tem de ser um arquivo.',
            'imagem.mimes' => 'O arquivo deve ser uma imagem dos tipos válidos, png, jpeg, jpg ou gif.',
        ];
    }
}
