<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories\CategoryTranslation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\ValidatorException;

class CategoryTranslationController extends Controller
{
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'string',
            'info_url' => 'string',
            'category_id' => 'required|integer:min:1',
            'locale' => [
                'required',
                'string',
                Rule::unique('category_translations')
                ->where('category_id',$request->input('category_id'))
                ->where('locale',$request->input('locale'))
            ]
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $result = CategoryTranslation::create($request->all());

        return response()->json([
            'data' => $result,
            'error' => 0
        ]);
    }

    public function getAll(Request $request) {
        $result = CategoryTranslation::all();

        return response()->json([
            'data' => $result,
            'error' =>0
        ]);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'description' => 'string',
            'info_url' => 'string',
            'parent_id' => 'string'
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $n = CategoryTranslation::where('id', $id)
        ->update($request->only(['title', 'description', 'info_url', 'parent_id']));

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }

    public function remove(Request $request, $id) {

        $n = CategoryTranslation::where('id', $id)->delete();

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
}
