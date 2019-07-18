<?php 
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class CategoriesComponent extends Component {
    private $Categories;
    private $Products;
    private $Images;
    private $ProductAttributes;
    public function initialize(array $config)
    {
        parent::initialize($config);  
        $this->Products = TableRegistry::getTableLocator()->get('Products');
        $this->Categories = TableRegistry::getTableLocator()->get('Categories');
        $this->Images=TableRegistry::getTableLocator()->get('Images');
        $this->ProductAttributes=TableRegistry::getTableLocator()->get('ProductAttributes');
        $this->connection = ConnectionManager::get('default');
    }

    public function selectAll(){
        $categories = $this->Categories->find('all')->where(['parent_id'=> 0])->toArray();
        foreach ($categories as $category) {
            $cate = $this->Categories->find('all')->where(['parent_id'=> $category['id']])->toArray();
            $category['options'] = $cate;
        }

        foreach ($category['options'] as $category) {
            $category['parent_name'] = $this->Categories->find()->where(['id'=>$category['parent_id']])->first()->name;
        }

        return $categories;
    }

    public function find($category_id){
        $category = $this->Categories->find()->where(['id'=>$category_id])->first();
        $nameParent = $this->Categories->find()->where(['id'=>$category->parent_id])->first();
        $category['nameParent'] = $nameParent->name;

        return $category;
    }

    public function selectChild(){
        $cateChilds = $this->Categories->find()->where(['parent_id !=' => 0])->toArray();
        $cateParents = $this->Categories->find()->where(['parent_id' => 0])->toArray();
        foreach ($cateChilds as $cateChild) {
            foreach ($cateParents as $cateParent) {
                if($cateChild->parent_id == $cateParent->id){
                    $cateChild['parent_name'] = $cateParent->name;
                }
            }
        }
        return $cateChilds;
    }

    public function checkParent($id = null){
        $parent_id = $this->Categories->find()->where(['id' => $id])->first()->parent_id;
        if($parent_id == 0){
            return 1;
        }else{
            return 0;
        }
    }

    public function update($reqCategory){
        $result = $this->Categories->query()->update()
        ->set(['name' => $reqCategory['name'],
               'parent_id' => $reqCategory['category'],
               'status' => $reqCategory['status']
              ])
        ->where(['id' => $reqCategory['id']])
        ->execute();
        return $result;
    }

    public function deleteProduct($id = null){
        $this->connection->begin();
            $products = $this->Products->find()->where(['category_id'=>$id])->toArray();

            foreach ($products as $product) {
                $images = $this->Images->find()->where(['product_id'=>$product['id']])->toArray();
                foreach ($images as $image) {
                    unlink('img/'.$image['name']);
                }
                $this->Images->query()->delete()->where(['product_id'=>$product['id']])->execute();
                $this->ProductAttributes->query()->delete()->where(['product_id'=>$product['id']])->execute();
                $this->Products->query()->delete()->where(['category_id' => $id])->execute();  
            }

        $result = $this->connection->commit();
        return $result;
    }
}
?>
