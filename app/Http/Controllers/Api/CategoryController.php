<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories\Category;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ValidatorException;
use Storage;

class CategoryController extends Controller
{
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'icon' => 'mimes:png,jpg,jpeg,svg|max:2048',
            'background_image' => 'mimes:png,jpg,jpeg,svg|max:2048',
            'avatar_image' => 'mimes:png,jpg,jpeg,svg|max:2048',
            'parent_id' => 'integer:min:1',
            'name' => 'required|string|max:256|unique:categories'
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }
        $new_fields = $request->only(['name', 'parent_id']);
        $new_fields['parent_id'] = intval($new_fields['parent_id']);
        $category = Category::create($new_fields);

        try {
            if($request->hasfile('icon')) {
                $file = $request->file('icon');
                $name = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/categories/icon', $name, 'public');
                $category->icon = "/".$filePath;
                $category->save();
            }
            if($request->hasfile('background_image')) {
                $file = $request->file('background_image');
                $name = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/categories/background_image', $name, 'public');
                $category->background_image = "/".$filePath;
                $category->save();
            }
            if($request->hasfile('avatar_image')) {
                $file = $request->file('avatar_image');
                $name = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/categories/avatar_image', $name, 'public');
                $category->avatar_image = "/".$filePath;
                $category->save();
            }
        }
        catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'error' => 2
            ]);
        }

        return response()->json([
            'data' => $category,
            'error' => 0
        ]);
    }
    public function getAll(Request $request) {

        $categories = Category::with(['childs'])
        ->get();

        return response()->json([
            'data' => $categories,
            'error' => 0,
        ]);
    }
    public function getSuperParent(Request $request) {
        $categories = Category::with(['childs'])->where('parent_id', null)
        ->get();

        return response()->json([
            'data' => $categories,
            'error' => 0,
        ]);
    }
    public function getById(Request $request, $id) {
        $category = Category::with(['childs'])->where('id', $id)->first();

        return response()->json([
            'data' => $category,
            'error' => 0
        ]);
    }
    public function getChilds(Request $request, $parent_id) {

        $category = Category::with(['childs'])->where('parent_id',  intval($parent_id))->first();

        return response()->json([
            'data' => $category,
            'error' => 0
        ]);
    }
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'icon' => 'mimes:png,jpg,jpeg,svg|max:2048',
            'background_image' => 'mimes:png,jpg,jpeg,svg|max:2048',
            'avatar_image' => 'mimes:png,jpg,jpeg,svg|max:2048',
            'parent_id' => 'integer:min:1',
            'name' => 'string|max:256|unique:categories'
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $category = Category::where('id', intval($id))->first();

        try {
            if($request->hasfile('icon')) {

                $file = $request->file('icon');
                $name = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/categories/icon', $name, 'public');

                if(Storage::exists("/public".$category->icon)){
                    Storage::delete("/public".$category->icon);
                }

                $category->icon = "/".$filePath;
                $category->save();
            }
            if($request->hasfile('background_image')) {
                $file = $request->file('background_image');
                $name = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/categories/background_image', $name, 'public');

                if(Storage::exists("/public".$category->background_image)){
                    Storage::delete("/public".$category->background_image);
                }

                $category->background_image = "/".$filePath;
                $category->save();
            }
            if($request->hasfile('avatar_image')) {
                $file = $request->file('avatar_image');
                $name = time().'_'.$file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/categories/avatar_image', $name, 'public');

                if(Storage::exists("/public".$category->avatar_image)){
                    Storage::delete("/public".$category->avatar_image);
                }

                $category->avatar_image = "/".$filePath;
                $category->save();
            }
        }
        catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'error' => 2
            ]);
        }
        $n = Category::where('id', intval($id))->update($request->only(['name', 'parent_id']));
        $category = Category::where('id', intval($id))->first();
        return response()->json([
            'data' => $category,
            'n' => $n,
            'error' => 0
        ]);
    }
    public function remove(Request $request, $id) {
        $n = Category::where('id',$id)->delete();

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
}
