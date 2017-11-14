<?php
namespace Tiup\LaravelTool\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
trait ControllerTrait{
	public function index(Request $request){
		return $this->model->search($request->all())->page();	
	}

	public function create(){
		return $this->model->toForm();
	}

	public function columns(){
		return $this->model->getHeader();
	}
	
	public function show($id){
		$model = $this->model->find($id);
		if($model){
			return $model->toForm();
		}else{
			$modelName = $this->model->getAttributeName('_name');
			return $this->response($modelName.'不存在',404);
		}
	}

	public function store(Request $request){
		$this->validator($request);
		try {
			$model = $this->model->create($request->all());
			return $model->toForm();
		} catch (\Exception $e) {
			return $this->response($e->getMessage(), 500);
		}
		
	}

	public function destroy($id){
		try {
			$ret = $this->model->destroy($id);
			return ['message' => '删除成功'];
		} catch (\Exception $e) {
			return $this->response($e->getMessage(), 500);
		}
	}

	public function delete(Request $request){
        $ids = $request->ids;
        if ($ids) {
            $users = $this->model->whereIn('id', $ids)->delete();
            return ['message' => '删除成功'];
        } else {
            return $this->response($e->getMessage(), 500);
        }
    }

	public function edit($id){
		$model = $this->model->find($id);
		return $model->toForm();
	}

	protected function validator(Request $request){
		$rules = [];
		$attributes = [];
		foreach ($this->model->attribute_info as $key => $value) {
			if(isset($value['rule'])){
				$rules[$key] = $value['rule'];
				$attributes[$key] = $this->model->getAttributeName($key);
			}
		}
		$this->validate($request, $rules, [], $attributes);
	}
	public function update(Request $request,$id){
		$this->validator($request);
		$model = $this->model->find($id);
		$model->fill($request->all());
		$model->save();
		$model = $this->attachMedia($model, $request);
		return $model->toForm();
	}

	public function response($data, $status = 200){
		if(is_string($data)){
			$data = ['message' => $data];
		}
		return response()->json($data, $status);
	}

	public function attachMedia($model, $request){
		foreach ($model->attribute_info as $key => $value) {
			if($value['type'] == 'media'){
				if($value['multiple']){
					$ids = Arr::pluck($request->{$key}, 'id');
				}else{
					$ids = $request->{$key}['id'];
				}
				
				$ret = $model->syncMedia($ids, $key);
			}
		}
		return $model;
	}
}