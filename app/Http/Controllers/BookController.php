<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return view('books', compact('books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'isbn' => 'required|unique:books',
            'price' => 'required',
        ]);

        $book = Book::create([
            'book_id' => 'TCBT_B_' . uniqid(),
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'price' => $request->price,
        ]);

        return redirect()->route('books')->with('success', 'Book added successfully');
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'isbn' => 'required|unique:books,isbn,' . $book->id,
            'price' => 'required',
        ]);

        $book->update($request->all());
        return redirect()->route('books')->with('success', 'Book updated successfully');
    }

    public function destroy($id)
    {
        Book::findOrFail($id)->delete();
        return redirect()->route('books')->with('success', 'Book deleted successfully');
    }

    public function makeAvailable($id)
    {
        $book = Book::findOrFail($id);
        $book->status = 'Available';
        $book->save();

        return redirect()->back()->with('success', 'Book marked as available.');
    }

}
