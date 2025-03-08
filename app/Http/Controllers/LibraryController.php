<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookHasStudent;
use App\Models\Student;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        $availableBooks = Book::where('status', 'Available')->get();
        $borrowedBooks = BookHasStudent::where('status', 'Borrowed')->with(['book', 'student'])->get();
        $overdueBooks = BookHasStudent::where('status', 'Borrowed')
            ->where('borrowed_at', '<', now()->subDays(14))
            ->with(['book', 'student'])
            ->get();

        return view('pages.home.LibraryOut', compact('availableBooks', 'borrowedBooks', 'overdueBooks'));
    }

    public function borrow(Request $request)
    {
        $student = Student::where('tcbt_student_number', $request->student_tcbt_number)->first();
        if (!$student) return back()->with('error', 'Student not found.');

        $book = Book::find($request->book_id);
        if (!$book || $book->status != 'Available') return back()->with('error', 'Book not available.');

        BookHasStudent::create([
            'book_id' => $book->id,
            'student_id' => $student->id,
            'borrowed_at' => $request->borrowed_at,
            'status' => 'Borrowed',
        ]);

        $book->update(['status' => 'Not Available']);
        return back()->with('success', 'Book borrowed successfully.');
    }

    public function returnBook($id)
    {
        $borrow = BookHasStudent::find($id);
        if (!$borrow) return back()->with('error', 'Record not found.');

        $borrow->update([
            'status' => 'Returned',
            'returned_at' => now(),
        ]);

        $borrow->book->update(['status' => 'Available']);
        return back()->with('success', 'Book returned successfully.');
    }

    public function history()
    {
        $returnedBooks = BookHasStudent::where('status', 'Returned')
            ->with(['book', 'student'])
            ->orderBy('returned_at', 'desc')
            ->get();

        return view('pages.home.LibraryHistory', compact('returnedBooks'));
    }


}
