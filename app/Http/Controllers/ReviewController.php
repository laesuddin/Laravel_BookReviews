<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    //This method will show review in backend
    public function index(Request $request) {
        $reviews = Review::with('book', 'user')->orderBy('created_at', 'DESC');
        if(!empty($request->keyword)){
            $reviewsv = $reviews->where('review', 'like', '%'.$request->keyword.'%');
        }
        $reviews= $reviews->paginate(10);
        return view('account.reviews.list', [
            'reviews' => $reviews
        ]);
    }
}
