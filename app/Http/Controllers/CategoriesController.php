<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    const countPerPage = 2;
    const regexEng  = "regex:/^[a-zA-Z]+$/";
    const regexEngRus = "regex:/^[a-zA-Za-яА-Я\-_]+$/";
    const regexNumber = "regex:/^[0-9]+$/";

    // Создать категорию
    public function add(Request $request)
    {
        $v = $validator = Validator::make($request->all(), [
                'id' => "required|string",
                'slug' => "required|unique:categories|string|".self::regexEng,
                'name' => "required|string|".self::regexEngRus,
                'description' => "string|".self::regexEngRus,
                'active' => "required|in:0,1"
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 200);
        }
        try {
            $data_id = $request->get('id');
            $data_slug = $request->get('slug');
            $data_name = $request->get('name');
            $data_desc = $request->get('description');
            $data_active = $request->get('active');
            DB::table('categories')->insert(
                array(
                    'id' => $data_id,
                    'slug' => $data_slug,
                    'name' => $data_name,
                    'description' => $data_desc,
                    'active' => $data_active
                )
            );
            return response()->json(['status' => 'success', 'response' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }

    // Удалить категорию
    public function delete(Request $request)
    {
        $v = $validator = Validator::make($request->all(), [
                'slug' => "required|string|".self::regexEng
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 200);
        }
        try {
            $cur_slug = $request->get('slug');
            DB::table('categories')->where('slug', '=', $cur_slug)->delete();
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }

    // Получить категорию по ID
    public function getByID(Request $request)
    {
        $v = $validator = Validator::make($request->all(), [
                'id' => "required|string"
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 200);
        }
        try {
            $cur_id = $request->get('id');
            $datas = DB::table('categories')->where('id', $cur_id)->first();
            return response()->json(['status' => 'success', 'response' => $datas], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }

    // Получить категорию по slug
    public function getBySlug(Request $request)
    {
        $v = $validator = Validator::make($request->all(), [
                'slug' => "required|string|".self::regexEng
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 200);
        }
        try {
            $cur_slug = $request->get('slug');
            $datas = DB::table('categories')->where('slug', $cur_slug)->first();
            return response()->json(['status' => 'success', 'response' => $datas], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }

    // Изменить категорию
    public function update(Request $request)
    {
        $v = $validator = Validator::make($request->all(), [
                'id' => "string",
                'slug' => "required|string|".self::regexEng, 
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
            ], 200);
        }
        try {
            $cur_slug = $request->get('slug');
            // Собираем поля и обновляем модель частично
            $ALLOW_NAMES = ['id', 'slug', 'name', 'description', 'createdDate', 'active'];
            $tmp_mass = [];
            if ($request->all())
            {
                foreach ( $request->all() as $item => $value ){
                    if (in_array($item, $ALLOW_NAMES)) $tmp_mass[$item] = $value;   // собираем только поля из массива разрешенных имен
                }
            }
            DB::table('categories')->where('slug', $cur_slug)->update($tmp_mass);
            return response()->json(['status' => 'success', 'response' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }

    private function fixBoolean($src)
    {
        if (($src === '0') || ($src === 'false')) { return 0; }
            else
        if (($src === '1') || ($src === 'true')) { return 1; }
    }

    private function stageOfInjection($text){
        $temp = trim($text);
        // Защита от sql инъекций есть во втроенном модуля запросов Laravel
        // Illuminate\Database\Eloquent\Builder
        return $temp;
    }

    public function getByFilter(Request $request)
    {
        // Некоторые параметры у валидатора закоментированы, 
        // чтобы можно было вписывать кейсы - с одними пробелами внутри
        $v = $validator = Validator::make($request->all(), [
                //'name' => "string", 
                //'description' => "string",
                'active' => "in:0,false,1,true",
                //'search' => "string",
                'pageSize' => "string|".self::regexNumber,
                'page' => "string|".self::regexNumber,
                'sort' => "in:id,slug,name,description,createdDate,active,
                              -id,-slug,-name,-description,-createdDate,-active"
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 200);
        }
        try {
            $cur_name = $this->stageOfInjection($request->get('name'));
            $cur_desc = $this->stageOfInjection($request->get('description'));
            $cur_active = $request->get('active');
            $requestedSort = $request->get('sort');
            $cur_page = $request->get('page');
            // Количество элементов на страницу
            $cur_pageSize = self::countPerPage;
            if ($request->get('pageSize'))
            {
                $cur_pageSize = $request->get('pageSize');
            }
            // По умолчанию сортировка: sort=-createdDate
            $requestedOrder = 'DESC';
            $requestedSortingType = 'createdDate';

            // Направление сортировки
            // sort=<name>
            if ($requestedSort)
            {
                $requestedOrder = 'ASC';
                $requestedSortingType = $requestedSort;
            }
            // sort=-<name>
            if (($requestedSort) && ($requestedSort[0] === '-'))
            {
                $requestedOrder = 'DESC';
                $requestedSortingType = substr($requestedSort, 1);  // выбираем поле по которому сортируем
            }
            // Формируем запрос
            $BUILD_QUERY = DB::table('categories');
            $cur_search = $request->get('search');
            if ($cur_search)
            {
                $BUILD_QUERY = $BUILD_QUERY->where('name', 'like', "%$cur_search%")->orWhere('description', 'like', "%$cur_search%");
            }
                else
            {
                if ($cur_name)
                {
                    $BUILD_QUERY = $BUILD_QUERY->where('name', 'like', "%$cur_name%");
                }
                if ($cur_desc)
                {
                    $BUILD_QUERY = $BUILD_QUERY->where('description', 'like', "%$cur_desc%");
                }
                if (isset($cur_active))
                {
                    $cur_active = $this->fixBoolean($cur_active);
                    $BUILD_QUERY = $BUILD_QUERY->where('active', '=', $cur_active);
                }
            }
            // Выполняем запрос
            $DATA_QUERY = $BUILD_QUERY->orderBy($requestedSortingType, $requestedOrder)->paginate($cur_pageSize);
            return response()->json([ 'status' => 'success', 'response' => $DATA_QUERY ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }
}