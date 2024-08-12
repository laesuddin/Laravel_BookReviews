<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    //This method will show home page 
    public function index(Request $request){
        $books =  Book::orderBy('created_at', 'DESC');
        if(!empty($request->keyword)){
            $books->where('title', 'like', '%' .$request->keyword. '%');
        }
        $books = $books->where('status', 1)->paginate(8);
        return view('home', [
            'books' => $books
        ]);
    }
    //This method will show book details page 
    public function details($id){
        $book = Book::with(['reviews.user', 'reviews' => function ($query) { 
                $query->where('status', 1); 
            }])->findOrFail($id);

        if($book->status == 0){
            abort(404);
        }
        $relatedBooks = Book::where('status', 1)->take(3)->where('id', '!=', $id)->inRandomOrder()->get();
        return view('book-details', [
                'book' => $book,
                'relatedBooks' => $relatedBooks
        ]);
    }
    public function saveReview(Request $request){
        $validator = Validator::make($request->all(), [
            'review' => 'required|min:10',
            'rating' => 'required'
        ]);
        
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        //Apply condition here
        $countReview = Review::where('user_id', Auth::user()->id)->where('book_id', $request->book_id)->count();
        if($countReview > 0){
            $request->session()->flash('error', 'You already submitted a review.');
            return response()->json([
                'status' => true,
            ]); 
        }

        $review = new Review();
        $review->review = $request->review; 
        $review->rating = $request->rating; 
        $review->user_id = Auth::user()->id; 
        $review->book_id = $request->book_id;
        $review->save();

        $request->session()->flash('success', 'Review submitted successfully. Wait for approval');
        return response()->json([
            'status' => true,
        ]);
    }
}
