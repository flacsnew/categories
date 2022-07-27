<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    const COUNT_PER_PAGE = 2;
    const REGEX_ENG  = "regex:/^[a-zA-Z]+$/";
    const REGEX_ENG_RUS = "regex:/^[a-zA-Za-яА-Я\-_]+$/";
    const REGEX_NUMBER = "regex:/^[0-9]+$/";

    // Создать категорию
    public function add(Request $request)
    {
        $v = Validator::make(
            $request->all(),
            [
                'id' => "required|string",
                'slug' => "required|unique:categories|string|".self::REGEX_ENG,
                'name' => "required|string|".self::REGEX_ENG_RUS,
                'description' => "string|".self::REGEX_ENG_RUS,
                'active' => "required|in:0,1"
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 400);
        }
        try {
            $dataId = $request->get('id');
            $dataSlug = $request->get('slug');
            $dataName = $request->get('name');
            $dataDesc = $request->get('description');
            $dataActive = $request->get('active');
            DB::table('categories')->insert(
                array(
                    'id' => $dataId,
                    'slug' => $dataSlug,
                    'name' => $dataName,
                    'description' => $dataDesc,
                    'active' => $dataActive
                )
            );
            return response()->json(['status' => 'success', 'response' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 400);
        }
    }

    // Удалить категорию
    public function delete(Request $request)
    {
        $v = Validator::make(
            $request->all(),
            [
                'slug' => "required|string|".self::REGEX_ENG
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 400);
        }
        try {
            $curSlug = $request->get('slug');
            DB::table('categories')->where('slug', '=', $curSlug)->delete();
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 400);
        }
    }

    // Получить категорию по ID
    public function getByID(Request $request)
    {
        $v = Validator::make(
            $request->all(),
            [
                'id' => "required|string"
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 400);
        }
        try {
            $curId = $request->get('id');
            $datas = DB::table('categories')->where('id', $curId)->first();
            return response()->json(['status' => 'success', 'response' => $datas], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 400);
        }
    }

    // Получить категорию по slug
    public function getBySlug(Request $request)
    {
        $v = Validator::make(
            $request->all(),
            [
                'slug' => "required|string|".self::REGEX_ENG
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 400);
        }
        try {
            $curSlug = $request->get('slug');
            $datas = DB::table('categories')->where('slug', $curSlug)->first();
            return response()->json(['status' => 'success', 'response' => $datas], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 400);
        }
    }

    // Изменить категорию
    public function update(Request $request)
    {
        $v = Validator::make(
            $request->all(),
            [
                'id' => "string",
                'slug' => "required|string|".self::REGEX_ENG,
                'name' => "string",
                'description' => "string",
                'createdDate' => "date",
                'active' => "in:0,1"
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 400);
        }
        try {
            $curSlug = $request->get('slug');
            // Собираем поля и обновляем модель частично
            $allowNames = ['id', 'slug', 'name', 'description', 'createdDate', 'active'];
            $tmp_mass = [];
            if ($request->all()) {
                foreach ($request->all() as $item => $value) {
                    if (in_array($item, $allowNames)) {
                        $tmp_mass[$item] = $value;
                    }   // собираем только поля из массива разрешенных имен
                }
            }
            DB::table('categories')->where('slug', $curSlug)->update($tmp_mass);
            return response()->json(['status' => 'success', 'response' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 400);
        }
    }

    private function fixBoolean($src)
    {
        if (($src === '0') || ($src === 'false')) {
            return 0;
        } elseif (($src === '1') || ($src === 'true')) {
            return 1;
        }
    }

    public function getByFilter(Request $request)
    {
        // Некоторые параметры у валидатора закоментированы,
        // чтобы можно было вписывать кейсы - с одними пробелами внутри
        $v = Validator::make(
            $request->all(),
            [
                //'name' => "string",
                //'description' => "string",
                'active' => "in:0,false,1,true",
                //'search' => "string",
                'pageSize' => "string|".self::REGEX_NUMBER,
                'page' => "string|".self::REGEX_NUMBER,
                'sort' => "in:id,slug,name,description,createdDate,active,
                              -id,-slug,-name,-description,-createdDate,-active"
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 400);
        }
        try {
            $curName = $request->get('name');
            $curDesc = $request->get('description');
            $curActive = $request->get('active');
            $requestedSort = $request->get('sort');
            //$cur_page = $request->get('page');
            // Количество элементов на страницу
            $curPageSize = self::COUNT_PER_PAGE;
            if ($request->get('pageSize')) {
                $curPageSize = $request->get('pageSize');
            }
            // По умолчанию сортировка: sort=-createdDate
            $requestedOrder = 'DESC';
            $requestedSortingType = 'createdDate';
            // Направление сортировки: sort=<name>
            if ($requestedSort) {
                $requestedOrder = 'ASC';
                $requestedSortingType = $requestedSort;
                // sort=-<name>
                if ($requestedSort[0] === '-') {
                    $requestedOrder = 'DESC';
                    $requestedSortingType = substr($requestedSort, 1);  // выбираем поле по которому сортируем
                }
            }
            // Формируем запрос
            $buildQuery = DB::table('categories');
            $curSearch = $request->get('search');
            if ($curSearch) {
                $buildQuery = $buildQuery->where('name', 'like', "%$curSearch%")->orWhere('description', 'like', "%$curSearch%");
            } else {
                if ($curName) {
                    $buildQuery = $buildQuery->where('name', 'like', "%$curName%");
                }
                if ($curDesc) {
                    $buildQuery = $buildQuery->where('description', 'like', "%$curDesc%");
                }
                if (isset($curActive)) {
                    $curActive = $this->fixBoolean($curActive);
                    $buildQuery = $buildQuery->where('active', '=', $curActive);
                }
            }
            // Выполняем запрос
            $dataQuery = $buildQuery->orderBy($requestedSortingType, $requestedOrder)->paginate($curPageSize);
            return response()->json([ 'status' => 'success', 'response' => $dataQuery ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 400);
        }
    }
}
