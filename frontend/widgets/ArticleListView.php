<?php
/**
 * Created by PhpStorm.
 * User: f
 * Date: 16/6/19
 * Time: 下午12:21
 */
namespace frontend\widgets;

use Yii;
use yii\helpers\Url;
use common\models\Category;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use yii\helpers\StringHelper;

class ArticleListView extends \yii\widgets\ListView
{

    public $layout = "{items}\n<div class=\"pagination\">{pager}</div>";
    public $pagerOptions = [
        'firstPageLabel' => '首页',
        'lastPageLabel' => '尾页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'options' => [
            'class' => '',
        ],
    ];
    public $template = "<article class='excerpt'>
                           <div class='focus'>
                               <a target='_blank' href='{article_url}'>
                                    <img width='186px' height='112px' class='thumb' src='{img_url}' alt='{title}'></a>
                           </div>
                           <header>
                               <a class='label label-important' href='{category_url}'>{category}<i class='label-arrow'></i></a>
                               <h2><a target='_blank' href='{article_url}' title='{title}'>{title}</a></h2>
                           </header>
                           <p class='auth-span'>
                               <span class='muted'><i class='fa fa-clock-o'></i> {pub_date}</span>
                               <span class='muted'><i class='fa fa-eye'></i> {scan_count}℃</span>
                               <span class='muted'><i class='fa fa-comments-o'></i> <a target='_blank' href='{comment_url}'>{comment_count}评论</a></span>
                           </p>
                           <span class='note'> {summary}</span>
                         </article>";

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->itemView = function ($model, $key, $index, $this){//var_dump($model);die;
            $url = Url::to(['article/view', 'id'=>$model->id]);
            $categoryName = $model->category?$model->category->name:'未分类';
            $categoryUrl = Url::to(['article/index', 'cat'=>$categoryName]);
            $pubTime = yii::$app->getFormatter()->asDate($model->created_at);
            if(!empty($model->thumb)){
                $imgUrl = "/timthumb.php?src=".$model->thumb."&h=112&w=168&zc=0";
            }else{
                $imgUrl = '/static/images/'.rand(1, 10).'.jpg';
            }
            $articleUrl = Url::to(['article/view', 'id'=>$model->id]);
            $summary = StringHelper::truncate($model->summary, 70);
            $title = StringHelper::truncate($model->title, 28);
            return str_replace(['{article_url}', '{img_url}', '{category_url}', '{title}', '{summary}', '{pub_date}', '{scan_count}', '{comment_count}', '{category}', '{comment_url}'],
                                [$articleUrl, $imgUrl, $categoryUrl, $title, $summary, date('Y-m-d', $model->created_at), $model->scan_count, $model->comment_count, $categoryName, $articleUrl."#comments"],
                                 $this->template);
        };
    }
    public function renderPager()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();
        $pager = array_merge($pager, $this->pagerOptions);
        return $class::widget($pager);
    }

}