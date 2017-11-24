<?php
namespace Tiup\LaravelTool\Traits;
trait MapperTrait{

	// protected $dm = '';
	// protected $name = '';
	// protected $name_en = '';
    public function scopeToMappers($query){
        static $mappers;
        if($mappers){
            return $mappers;
        }
    	$mappers = $query->get();
        if(!empty($this->dm)){
            $mappers = $mappers->map(function($item){
                $new = [
                    'id' => $item->{$item->dm},
                    'name' => $item->{$item->name},
                    'name_en' => $item->{$item->name_en}
                ];
                return $new;
            });
        }
    	return $mappers;
    }

    //id转换代码
    public static function trans($id){
        static $id2names;
        if(empty($id2names)){
            $mappers = static::toMappers();
            foreach ($mappers as $key => $mapper) {
                $id2names[$mapper['id']] = $mapper['name'];
            }
        }
        return isset($id2names[$id]) ? $id2names[$id] : '未知';
    }

    public static function parse($name){
        static $name2ids;
        if(empty($name2ids)){
            $mappers = static::toMappers();
            foreach ($mappers as $key => $mapper) {
                $id2names[$mapper['name']] = $mapper['id'];
            }
        }
        return isset($id2names[$name]) ? $id2names[$name] : false;
    }
}

