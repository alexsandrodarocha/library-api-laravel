<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ReportService;

class BorrowingController extends Controller
{
    public function reports(Request $request)
    {
        $report = $request->report;
        switch ($report) {
            case 'userwithmostbooks':
                $loans = ReportService::userWithMostBooks();
                $user = User::find($loans[0]);
                return response()->json(['success' => true, 'msg' => 'Relatório retornado com sucesso.', 'data' => ['Usuario com maior o número de livros em empréstimo.' => $user->name, 'Quantidade de livros' => $loans[1]]]);
                break;
            case 'authorwithmostbooks':
                $loans = ReportService::authorWithMostBooks();
                $author = Author::find($loans[0]);
                return response()->json(['success' => true, 'msg' => 'Relatório retornado com sucesso.', 'data' => ['Autor com maior número de livros em empréstimo.' => $author->name, 'Quantidade de livros' => $loans[1]]]);
                break;
            case 'userswithopenreturn':
                $users = ReportService::usersWithOpenReturn();
                $userIdArray = [];
                $addedUsers = [];
                foreach ($users as $userId) {
                    $usuario = User::find($userId);
                    if (!in_array($usuario->name, $addedUsers)) {
                        $userIdArray[] = $usuario->name;
                        $addedUsers[] = $usuario->name;
                    }
                }
                return response()->json(['success' => true, 'msg' => 'Relatório retornado com sucesso.', 'Lista de usuários com empréstimo em aberto' => $userIdArray]);
            case 'userswithexpiredloan':
                $users = ReportService::usersWithExpiredLoan();
                $userIdArray = [];
                foreach ($users as $userId) {
                    $usuario = User::find($userId);
                    $userIdArray[] = $usuario->name;
                }
                return response()->json(['success' => true, 'msg' => 'Relatório retornado com sucesso.', 'Lista de usuários com empréstimo expirado:' =>  $userIdArray]);
                break;
            case 'userswithnoloan':
                $users = ReportService::usersWithNoLoan();
                $userIdArray = [];
                foreach ($users as $userId) {
                    $usuario = User::find($userId);
                    $userIdArray[] = $usuario->name;
                }
                return response()->json(['success' => true, 'msg' => 'Relatório retornado com sucesso.', 'Lista de usuários sem empréstimos:' =>  $userIdArray]);
                break;
            case 'bookwithmostloans':
                $loans = ReportService::bookWithMostLoans();
                $book = Book::find($loans[0]);
                return response()->json(['success' => true, 'msg' => 'Relatório retornado com sucesso.', 'data' => ['Livro com maior número de empréstimos' => $book->title, 'Quantidade de emprestimos' => $loans[1]]]);
                break;

            default:
                return response()->json(['success' => false, 'msg' => 'Relatório não encontrado.'], 400);
                break;
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = User::with('books')->get();
        return $loans;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'book_id' => 'required|exists:books,id',
                'due_date' => 'required|date'
            ]);

            $user = User::find($request->user_id);

            $user->books()->attach($request->book_id, [
                'borrowed_at' =>  now(),
                'due_date' => $request->due_date
            ]);

            return response()->json(['success' => true, 'msg' => 'Emprestimo Realizado com sucesso!', 'data' => $user->books], 201);

        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'msg' => $th->getMessage() ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $user_id, int $book_id)
    {
        try {
            $user = User::find($user_id);

            $book = $user->books()->where('book_id', $book_id)->firstOrFail();

            return $book;
        } catch (\Throwable $th) {
            return  $th->getMessage();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $user_id, int $book_id)
    {
        try {
            $request->validate([
                'due_date' => 'required|date'
            ]);

            $user = User::findOrFail($user_id);
            $user->books()->updateExistingPivot($book_id, [
                'due_date' => $request->due_date
            ]);

            return  $user->books()->where('book_id', $book_id)->first();
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $user_id, int $book_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $user->books()->detach($book_id);

            return 'Empréstimo removido com sucesso!';
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }
}
