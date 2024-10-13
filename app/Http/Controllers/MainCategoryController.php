<?php

namespace App\Http\Controllers;

use App\Models\MainCategory;
use Illuminate\Http\Request;

class MainCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mainCategory = MainCategory::orderBy('id', 'desc')->paginate(10);
        // return $category;
        return view('backend.main-category.index')->with('mainCategories', $mainCategory);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.main-category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|required',
            'photo'=>'string|required',
        ]);

        $mainCategory = new MainCategory();

        $mainCategory->name = $request->name;
        $mainCategory->photo = $request->photo;
        $status = $mainCategory->save();

        if($status){
            toast('Main Category Added Successfully!','success');
        }else{
            toast('Error Occurred, Please try again!','error');
        }

        return redirect()->route('main-category.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(MainCategory $mainCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MainCategory $mainCategory)
    {
        return view('backend.main-category.edit', ['mainCategory' => $mainCategory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MainCategory $mainCategory)
    {
        $request->validate([
            'name' => 'required',
            'photo'=>'string|required',
        ]);

        $mainCategory->name = $request->name;
        $mainCategory->photo = $request->photo;
        $status = $mainCategory->save();

        if($status){
            toast('Main Category Updated Successfully!','success');
        }else{
            toast('Error occurred, Please try again!','error');
        }

        return redirect()->route('main-category.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MainCategory $mainCategory)
    {
        // dd($mainCategory->subCategories);
        $subCategories = $mainCategory->subCategories;
        if(count($subCategories) > 0){
            toast('You cannot delete this main category because it has associated categories!','error');
            return redirect()->route('main-category.index');
        }

        $status = $mainCategory->delete();
        
        if($status){
            toast('Category successfully deleted','success');
        }
        else{
            toast('Error while deleting category','error');
        }
        return redirect()->route('main-category.index');
    }
}
