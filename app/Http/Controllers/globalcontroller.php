<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\tags;
use App\Models\category;
use App\Models\User;
use App\Models\Blog;
use App\Models\Blogtag;
use App\Models\Blogcategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class globalcontroller extends Controller
{
    public function index(Request $request){
        if(!Auth::check() && $request->path() != 'login'){
            return redirect('/login');
        }
        if(!Auth::check() && $request->path() == 'login'){
            return view('welcome');
        }

            $user = Auth::user();

     if($user->userType == 'user'){
                return redirect('/login');
             }
        if($request->path() == 'login'){
                return redirect('/');
             }
        return view('welcome');     

    }
    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
    public function addtags(Request $request){
        $this->validate($request, [
            'tagName' => 'required',
        ]);
        return tags::create([
            'tagName' => $request->tagName,
        ]);
    }

    public function edittags(){
        $this->validate($request, [
            'tagName' => 'required',
            'id' => 'required'
        ]);
        return tags::where('id', $request->id)->update([
            'tagName' => $request->tagName
        ]);
    }
    public function gettags(){
        return tags::orderBy('id', 'desc')->get();
    }
    public function addCategory(Request $request){
        $this->validate($request, [
            'categoryName' => 'required',
            'iconImage' => 'required'
        ]);
        return category::create([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage
        ]);
        $picName = time().'.'.$request->image->extension();
        $request->image->move(public_path('uploads'),  $picName);
        $request->iconImage = $picName;
            // return response()->json([
            //     'success' => 1,
            //     'file' => [
            //         'url' => "http://stack.test/uploads/$picName",
            //     ],
            // ]);
    }
    public function editCategory(Request $request){
        $this->validate($request, [
            'id' => 'required',
            'categoryName' => 'required',
            'iconImage' => 'required'
        ]);
        return category::where('id', $request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage
        ]);
    }
    public function deleteCategory(Request $request){
        // first delete the original image from server
        $this->deleteFileFromServer($request->iconImage);
        $this->validate($request, [
            'id' => 'required',
        ]);
        return category::where('id', $request->id)->delete();
    }
        public function Upload(Request $request){
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,jpg,png',
        ]);
       $picName = time().'.'.$request->file->extension();
       $request->file->move(public_path('uploads'),  $picName);
       return  $picName;
    }
        public function deleteImage(Request $request)
    {
        $fileName = $request->imageName;
        $this->deleteFileFromServer($fileName, false);
        return 'done';
    }
       public function deleteFileFromServer($fileName, $hasFullPath = false)
    {
        if (!$hasFullPath) {
            $filePath = public_path() . '/uploads/' . $fileName;
        }
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        return;
    }
        public function getCategory()
    {
        return category::orderBy('id', 'desc')->get();
    }
    public function createUser(Request $request){
        $this->validate($request, [
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'userType' => 'required',
        ]);
          $password = bcrypt($request->password);
          $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            'userType' => $request->userType
        ]);
        return $user;
    }
    public function getUser(){
        return User::get(); 
        // return User::where('role_id', '!=', 'User')->get();
    }
    public function editUser(Request $request){
        $this->validate($request, [
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",
            'password' => 'min:6',
            'userType' => 'required',
            // 'role_id' => 'required',
        ]);
        $data = [
            'fullName' => $request->fullName,
            'email' => $request->email,
            'userType' => $request->userType,
        ];
        if ($request->password) {
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }
        $user = User::where('id', $request->id)->update($data);
        return $user;
    }
    public function adminLogin(Request $request){
        $this->validate($request, [
            'email' => "bail|required|email",
            'password' => 'bail|required|min:6',
        ]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            # code...
            $user = Auth::User();
            // dd($user->role());
            // return $user->role->roleName;
            if($user->userType == 'user'){
                Auth::logout();
                return response()->json([
                    'msg' => 'incorrect user credentials'
                ], 401);
            };
            // if($user->role->isAdmin == 0){
            //     Auth::logout();
            //     return response()->json([
            //         'msg' => 'incorrect user credentials'
            //     ], 401);
            // };
            return response()->json([
                'msg' => 'You have logged in succesfully',
                'User' => $user 
            ]);
        }else{
            return response()->json([
                'msg' => 'incorrect user credentials'
            ], 401); 
        }
    }
    public function uploadEditorImage(Request $request){
        $this->validate($request, [
                    'image' => 'required|mimes:jpeg,jpg,png',
                ]);
               $picName = time().'.'.$request->image->extension();
               $request->image->move(public_path('uploads'),  $picName);
              return response()->json([
                    'success' => 1,
                    'file' => [
                        'url' => "http://stack.test/uploads/$picName",
                    ],
                ]);
               return  $picName;
    }
    public function createBlog(Request $request){
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'category_id' => 'required',
            'tags_id' => 'required',
            'jsonData' => 'required'
        ]);
        $categories = $request->category_id;
        $tags = $request->tags_id;
        $blogCategories = [];
        $blogTags = [];
     
        // \Log::info($blogCategories);
        DB::beginTransaction();
        try {
            //code...
             $blogs = Blog::create([
            'title' => $request->title,
            'slug' => $request->title,
            'post' => $request->post,
            'post_excerpt' => $request->post_excerpt,
            'user_id' => Auth::user()->id,
            'metaDescription' => $request->metaDescription,
            'jsonData' => $request->jsonData,
        ]);
        // insert blog categrories
        foreach($categories as $c){
            array_push($blogCategories, ['category_id' => $c, 'blog_id' => $blogs->id]);
          }
          Blogcategory::insert( $blogCategories);
        //   insert blo tags
        foreach($tags as $t){
            array_push($blogTags , ['tags_id' => $t, 'blog_id' => $blogs->id]);
          }
          Blogtag::insert($blogTags);
          DB::commit();
          return 'done bro';
        } catch (\Throwable $e) {
            // return $e;
            DB::rollback();
            return 'Not done';
        }
       
    }
    public function updateBlog(Request $request, $id){
        $this->validate($request, [
            'title' => 'required',
            'post' => 'required',
            'post_excerpt' => 'required',
            'metaDescription' => 'required',
            'category_id' => 'required',
            'tags_id' => 'required',
            'jsonData' => 'required'
        ]);
        $categories = $request->category_id;
        $tags = $request->tags_id;
        $blogCategories = [];
        $blogTags = [];
     
        // \Log::info($blogCategories);
        DB::beginTransaction();
        try {
            //code...
             $blogs = Blog::where(id, $id)->update([
            'title' => $request->title,
            'slug' => $request->title,
            'post' => $request->post,
            'post_excerpt' => $request->post_excerpt,
            'user_id' => Auth::user()->id,
            'metaDescription' => $request->metaDescription,
            'jsonData' => $request->jsonData,
        ]);
        // insert blog categrories
        foreach($categories as $c){
            array_push($blogCategories, ['category_id' => $c, 'blog_id' => $id]);
          }
          Blogcategory::where('blog_id', $id)->delete();
          Blogcategory::insert( $blogCategories);
        //   insert blo tags
        foreach($tags as $t){
            array_push($blogTags , ['tags_id' => $t, 'blog_id' => $id]);
          }
          Blogtag::where('blog_id', $id)->delete();
          Blogtag::insert($blogTags);
          DB::commit();
          return 'done bro';
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return 'Not done';
        }
    }
    public function blogdata(Request $request){
        // return Blog::with(['tag', 'cat'])->get();
        return Blog::with(['tag', 'cat'])->orderBy('id', 'desc')->paginate($request->total);
    }
    public function deleteBlog(Request $request)
    {
        return Blog::where('id', $request->id)->delete();
    }
    public function updateSingleBlog(Request $request, $id){
        return Blog::with(['tag', 'cat'])->where('id', $id)->first();
    }
}
