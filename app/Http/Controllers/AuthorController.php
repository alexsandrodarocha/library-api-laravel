<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $authors = Author::with('books')->get();

      return response()->json(['success' => true, 'data' => $authors]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
           $request->validate([
            'name' => 'required'
           ],
           [
            'required' => 'O campo :atribute é obrigatório!'
           ]);

           $author = Author::create($request->all());

           return response()->json(['success' => true, 'msg' => 'autor criado com sucesso!', 'data' => $author]);

        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'msg' =>  $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $author = Author::with('books')->findOrFail($id);

            return response()->json(['success' => true, 'data' => $author]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, "msg" => $th->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
             'name' => 'required'
            ],
            [
             'required' => 'O campo :atribute é obrigatório!'
            ]);

            $author = Author::findOrFail($id);

           $author->fill([
                'name' => $request->name
           ]);

           $author->save();

            return response()->json(['success' => true, 'msg' => 'autor ediatado com sucesso!', 'data' => $author]);

         } catch (\Throwable $th) {
             return response()->json(['success' => false, 'msg' =>  $th->getMessage()]);
         }
    }



    public function destroy(string $id)
    {
       $author = Author::findOrFail($id);

       $author->delete();

       return response()->json(['success' => true, 'msg' => 'autor deletado!']);
    }
}
