<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BookController extends Controller
{
    //This method will show books listing page
    public function index(Request $request){
        $books =  Book::orderBy('created_at', 'DESC');
        if(!empty($request->keyword)){
            $books->where('title', 'like', '%'.$request->keyword.'%');
        }
        $books = $books->paginate(10);
        return view('books.list', [
            'books' => $books
        ]);
    }
    //This method will show create book page 
    public function create(){
        return view('books.create');
    }
    //This method will store a book in database
    public function store(Request $request){

        $rules = [
            'title' => 'required|min:5',
            'author' => 'required|min:3',
            'status' => 'required',
        ];
        if(!empty($request->image)){
            $rules['image'] = 'image';
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route('books.create')->withInput()->withErrors($validator);
        }

        //Save book in database
        $book = new Book;
        $book->title = $request->title;
        $book->author = $request->author;
        $book->description = $request->description;
        $book->status = $request->status;
        $book->save();

        //Upload book image here
        if (!empty($request->image)) {
            //Delete old image 
            File::delete(public_path('uploads/books/'.$book->image));
            File::delete(public_path('uploads/books/thumb/'.$book->image));

            $image = $request->image;
            $extension = $image->getClientOriginalExtension();
            $imageName = time().'.'.$extension;
            $image->move(public_path('uploads/books'), $imageName);
            $book->image = $imageName;
            $book->save();

            // create new image instance
            $manager = new ImageManager(Driver::class);
            $img = $manager->read(public_path('uploads/books/'.$imageName));
            $img->resize(990);
            $img->save(public_path('uploads/books/thumb/'.$imageName));
        }  
        return redirect()->route('books.index')->with('success', 'Book added successfully.');

    }
    //This method will show edit book page
    public function edit(){

    }
    //This method will update a book
    public function update(){

    }
    //This method will delete a book from database 
    public function destroy(){

    }
}
