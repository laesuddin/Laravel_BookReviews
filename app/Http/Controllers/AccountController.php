<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{   
    //This method will show register page
    public function register(){
        return view('account.register');
    }

    //This method will register a user
    public function processRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:5',
            'password_confirmation' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->route('account.register')->withInput()->withErrors($validator);
        }

        //Now register user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('account.login')->with('success', 'You have registerd successfully.');
    }
    public function login(){
        return view('account.login');
    }
    public function authenticate(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->route('account.login')->withInput()->withErrors($validator);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return redirect()->route('account.profile');
        }else{
            return redirect()->route('account.login')->with('error', 'Email/Password is incorrect.');
        }
    }
    public function profile(){
        $user = User::find(Auth::user()->id);
        return view ('account.profile',[
            'user' => $user
        ]);
    }
    // This method will update user profile
    public function updateProfile(Request $request){

        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.Auth::user()->id.',id',
        ];

        if(!empty($request->image)){
            $rules['image'] = 'image';
        }
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('account.profile')->withInput()->withErrors($validator);
         }
        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        //Here we will upload image
        if (!empty($request->image)) {
            //Delete old image 
            File::delete(public_path('uploads/profile/'.$user->image));
            File::delete(public_path('uploads/profile/thumb/'.$user->image));

            $image = $request->image;
            $extension = $image->getClientOriginalExtension();
            $imageName = time().'.'.$extension;
            $image->move(public_path('uploads/profile'), $imageName);
            $user->image = $imageName;
            $user->save();

            // create new image instance
            $manager = new ImageManager(Driver::class);
            $img = $manager->read(public_path('uploads/profile/'.$imageName));
            $img->cover(150, 150);
            $img->save(public_path('uploads/profile/thumb/'.$imageName));
        }   
        return redirect()->route('account.profile')->with('success', 'Profile Updated Successfully.');
    }
    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function myReviews(Request $request){

        $reviews = Review::with('book')->where('user_id', Auth::user()->id);
        $reviews = $reviews->orderBy('created_at', 'DESC');
        if(!empty($request->keyword)){
            $reviewsv = $reviews->where('review', 'like', '%'.$request->keyword.'%');
        }
        $reviews = $reviews->paginate(10);

        return view('account.my-reviews.list',[
            'reviews' => $reviews
        ]);
    }
    //This method will show edit review page
    public function editReview($id){
        $review = Review::where([
            'id' => $id,
            'user_id' => Auth::user()->id
        ])->with('book')->first();

        return view('account.my-reviews.edit',[
            'review' => $review
        ]);
    }
      //This method will update a review
      public function updateReview($id, Request $request){
        $review = Review::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'review' => 'required',
            'rating' => 'required'
        ]);
        if($validator->fails()){
            return redirect()->route('account.my-reviews.editReview', $id)->withInput()->withErrors($validator);
        }
        $review->review =  $request->review;
        $review->rating = $request->rating;
        $review->save();
        $request->session()->flash('success', 'Review updated successfully.');
        return redirect()->route('account.my-reviews');
    }
}
