<?php
namespace Tiup\LaravelTool\Traits;
use Input;
trait ModelTrait{

	/**
	 * 根据条件检索数据。 待完善
	 * 过滤空值、生成搜索条件。
	 */
	public function scopeSearch($query, $condition){
		if(isset($condition['query'])){
			$query = $this->_query($condition['query']);
		}
		return $query;
	}

	/**
	 * 搜索接口
	 **/
	public function scope_Query($query, $keyword){
		if($keyword){
			$query->where('name','like','%'.$keyword.'%');
		}
		return $query;
	}
	//分页查询
	public function scopePage($query, $page = null, $perpage = null){

		if(empty($page)){
			$page = Input::get('page', 1);
		}
		if(empty($perpage)){
			$perpage = Input::get('perpage', 10);
		}
		
		$total = $query->count();
		$result = $query->forPage($page, $perpage)->get();
		return array('total' => $total, 'data' => $result);
	}

	/**
	 * 获取类名
	 **/
	public function getName(){
		$path = explode('\\', __CLASS__);
		return array_pop($path);
	}

	/**
	 * 生成列表，带头部
	 **/
	public function scopeGetHeader(){
		$columns = [];
		if($this->attribute_info){
			foreach ($this->attribute_info as $key => $value) {
				if(isset($value['table']) && $value['table'] == true){
					$column = [
						'title' => $this->getAttributeName($key),
						'key' => $key,
					];
					$columns[] = $column;
				}
			}
		}
		return $columns;
	}

	/**
	 * 获取字段的名称
	 **/
	public function getAttributeName($attribute){
		$name = $this->getName();
		$name = strtolower($name);
		$lang = 'model.'.$name.'.'.$attribute;
		return __($lang);
	}

	/**
	 * 生成表单
	 **/
	public function toForm(){
		if($this->use_form == false){
			return $this;
		}
		
		$name = $this->getName();
		$name = strtolower($name);
		$mapper_types = ['radio','select','checkbox'];
		foreach ($this->attribute_info as $key => $value) {
			$lang = 'model.'.$name.'.'.$key;
			$value['key'] = $key;
			$value['name'] = __($lang);
			$value['value'] = $this->$key;
			if(in_array($value['type'], $mapper_types)){
				if(isset($value['mapper']) && class_exists($value['mapper'])){
					$class = new $value['mapper'];
					$mappers = $class->toMappers();
					$value['mappers'] = $mappers;
				}
			}
			$form[] = $value;
		}
		return $form;
	}

	public function scopeTableList($query){
		$fields = [];
		if($this->attribute_info){
			foreach ($this->attribute_info as $key => $value) {
				if(isset($value['table']) && $value['table'] == true){
					$fields[] = $key;
				}
			}
		}
		if($fields){
			$query->select($fields);
		}
	}
}

