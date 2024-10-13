<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\MainCategory;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::getAllCategory();
        $mainCategory = MainCategory::get();
        // return $category;
        return view('backend.category.index', ['categories' => $category, 'mainCategories' => $mainCategory]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_cats=Category::where('is_parent',1)->orderBy('title','ASC')->get();
        $mainCategory = MainCategory::get();
        return view('backend.category.create', ['parent_cats' => $parent_cats, 'mainCategories' => $mainCategory]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $this->validate($request,[
            'title'=>'string|required',
            'main_category_id' => 'required',
            'summary'=>'string|nullable',
            'photo'=>'string|nullable',
            'status'=>'required|in:active,inactive',
            'is_parent'=>'nullable',
        ]);
        $data= $request->all();
        $slug=Str::slug($request->title);
        $count=Category::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $data['is_parent'] = $request->input('is_parent',0);
        // return $data;   
        $status=Category::create($data);
        if($status){
            toast('Category Added Successfully!','success');
        }
        else{
            toast('Error occurred, Please try again!','error');
        }
        return redirect()->route('category.index');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parent_cats=Category::where('is_parent',1)->get();
        $mainCategories = MainCategory::get();
        $category=Category::findOrFail($id);
        return view('backend.category.edit', ['category' => $category, 'parent_cats' => $parent_cats, 'mainCategories' => $mainCategories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $category=Category::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required',
            'main_category_id' => 'required',
            'summary'=>'string|nullable',
            'photo'=>'string|nullable',
            'status'=>'required|in:active,inactive',
        ]);
        $data= $request->all();
        // dd($data);
        $data['is_parent']=$request->input('is_parent',0);
        // return $data;

        $status=$category->fill($data)->save();

        if($status){
            toast('Category Updated Successfully!','success');
        }
        else{
            toast('Error occurred, Please try again!','error');
        }
        return redirect()->route('category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::with('products')->findOrFail($id);
        $child_cat_id=Category::where('parent_id',$id)->pluck('id');
        if($category && count($category->products) > 0){
            toast('Category has products, cannot be deleted!','error');
            return redirect()->route('category.index');
        }

        $status=$category->delete();
        
        if($status){
            if(count($child_cat_id)>0){
                Category::shiftChild($child_cat_id);
            }
            toast('Category successfully deleted','success');
        }
        else{
            toast('Error while deleting category','error');
        }
        return redirect()->route('category.index');
    }

    public function getChildByParent(Request $request){
        // return $request->all();
        $category=Category::findOrFail($request->id);
        $child_cat=Category::getChildByParentID($request->id);
        // return $child_cat;
        if(count($child_cat)<=0){
            return response()->json(['status'=>false,'msg'=>'','data'=>null]);
        }
        else{
            return response()->json(['status'=>true,'msg'=>'','data'=>$child_cat]);
        }
    }
}
