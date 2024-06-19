<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public static function userWithMostBooks()
    {
        $tableResult = DB::table('book_user')
            ->select('user_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(1)
            ->get();

        $userId = $tableResult->pluck('user_id')->first();
        $count  = $tableResult->pluck('count')->first();

        return [$userId, $count];
    }

    public static function authorWithMostBooks()
    {
        $tableResult = DB::table('books')
            ->select('author_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('author_id')
            ->orderBy('count', 'desc')
            ->limit(1)
            ->get();

        $authorId = $tableResult->pluck('author_id')->first();
        $count  = $tableResult->pluck('count')->first();
        return [$authorId, $count];
    }
    public static function usersWithOpenReturn()
    {
        $tableResult = DB::table('book_user')
            ->where('due_date', '>', Carbon::today())
            ->get();
        $usersLoansOpen = $tableResult->pluck('user_id');
        return $usersLoansOpen;
    }
    public static function usersWithExpiredLoan()
    {
        $tableResult = DB::table('book_user')
            ->where('due_date', '<', Carbon::today())
            ->get();

        $usersLoansExpired = $tableResult->pluck('user_id');
        return $usersLoansExpired;
    }
    public static function usersWithNoLoan()
    {
        $tableLoans = DB::table('book_user')
            ->select('user_id')
            ->get();

        $tableUsers = DB::table('users')
            ->select('id')
            ->get();

        $users = $tableUsers->pluck('id');
        $usersWithLoans = $tableLoans->pluck('user_id');

        $missingUsersIds = $users->diff($usersWithLoans);

        return $missingUsersIds;
    }
    public static function bookWithMostLoans()
    {
        $bookMostLoans = DB::table('book_user')
            ->select('book_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('book_id')
            ->orderBy('count', 'desc')
            ->limit(1)
            ->get();


        $bookId = $bookMostLoans->pluck('book_id')->first();
        $count  = $bookMostLoans->pluck('count')->first();

        return [$bookId, $count];
    }
}
