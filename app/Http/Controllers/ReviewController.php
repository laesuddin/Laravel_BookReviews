<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    //This method will show edit review page
    public function edit($id) {
        $review = Review::findOrFail($id);
        return view('account.reviews.edit', [
            'review' => $review
        ]);
    }
    //This method will update a review
    public function updateReview($id, Request $request){
        $review = Review::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'review' => 'required',
            'status' => 'required'
        ]);
        if($validator->fails()){
            return redirect()->route('account.reviews.edit', $id)->withInput()->withErrors($validator);
        }
        $review->review =  $request->review;
        $review->status = $request->status;
        $review->save();
        $request->session()->flash('success', 'Review updated successfully.');
        return redirect()->route('account.reviews');
    }
    //This method will delete a review
    public function deleteReview(Request $request){
        $id = $request->id;
        $review = Review::find($id);
        if($review == null){
            $request->session()->flash('error', 'Review not found.');
            return response()->json([
                'status' => false
            ]);
        }else{
            $review->delete();
            $request->session()->flash('success', 'Review deleted successfully.');
            return response()->json([
                'status' => true
            ]);
        }
    }
}
