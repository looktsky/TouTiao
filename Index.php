<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    /**
     * @desc               首页
     * @return string
     * @time 2018/1/6 15:41
     * @author LTK 617209350@qq.com
     */
    public function index()
    {
        $url = 'http://isub.snssdk.com/article/v1/tab_comments/?group_id=6507586637612974599&item_id=6507586637612974599&aggr_type=1&count=1&offset=0&tab_index=0&iid=18415669017&device_id=41915130930&ac=wifi&channel=jiawo2&aid=13&app_name=news_article&version_code=499&version_name=4.9.9&device_platform=android&ssmix=a&device_type=GT-I9060C&os_api=19&os_version=4.4.2&uuid=379650010925175&openudid=5c514fe4fcd77606&manifest_version_code=500r';
        $data = $this->getData($url);
        $this->assign('total_number',$data->total_number);

        $my_number = Db::name('tt_user_all')->count();
        $this->assign('my_number',$my_number);
        return $this->view->fetch();
    }

    /**
     * @desc               从头条爬取评论数据
     * @time 2018/1/6 15:41
     * @author LTK 617209350@qq.com
     */
    public function getToutiaoList()
    {
        return false;
        $cu = input('offset');
        $offset = (int)$cu;

        $url = "http://isub.snssdk.com/article/v1/tab_comments/?group_id=6507586637612974599&item_id=6507586637612974599&aggr_type=1&count=50&offset=".$offset."&tab_index=0&iid=18415669017&device_id=41915130930&ac=wifi&channel=jiawo2&aid=13&app_name=news_article&version_code=499&version_name=4.9.9&device_platform=android&ssmix=a&device_type=GT-I9060C&os_api=19&os_version=4.4.2&uuid=379650010925175&openudid=5c514fe4fcd77606&manifest_version_code=500r";
        $data = $this->getData($url);

        $time = time();
        $tt = Db::name('tt_user_all');

        // 对返回数据进行处理
        if(!empty($data->data)){
            $all = 0;
            $more = 0;
            $list = $data->data;

            $rt = [];

            foreach ($list as $k=>$v){
                $user = $v->comment;
                $add['user_id'] = $user->user_id;
                $add['reply_count'] = $user->reply_count;
                $add['user_name'] = $user->user_name;
                $add['score'] = $user->score;
                $add['is_pgc_author'] = $user->is_pgc_author;
                $add['user_profile_image_url'] = $user->user_profile_image_url;
                $add['text'] = $user->text;
                $add['create_time'] = $user->create_time;
                $add['add_time'] = $time;
                $add['offset'] = $offset;

                if($add['user_name'] == '用户55991757202'){
                    cache('url',$url);
                }
                
                // 判断用户是否已存在
                $id = $tt->where('text',$add['text'])->column('id');

                if(!$id){
                    $more ++;

                    // 获取其他信息
                    $url = 'http://isub.snssdk.com/2/user/profile/v2/?user_id='.$add['user_id'].'&iid=18415669017&device_id=41915130930&ac=wifi&channel=jiawo2&aid=13&app_name=news_article&version_code=499&version_name=4.9.9&device_platform=android&ssmix=a&device_type=GT-I9060C&os_api=19&os_version=4.4.2&uuid=379650010925175&openudid=5c514fe4fcd77606&manifest_version_code=500';

                    $user3 =  $this->getData($url);
                    $user3 = $user3->data;
                    $add['description'] = $user3->description;
                    $add['bg_img_url'] = isset($user3->bg_img_url)?$user3->bg_img_url:'';
                    $add['share_url'] = isset($user3->share_url)?$user3->share_url:'';
                    $add['mobile'] = isset($user3->mobile)?$user3->mobile:'';
                    $tt->insert($add);
                }

                // 获取第一个评论的人
                if($v->comment->reply_list){
                    $user2 = $v->comment->reply_list[0];

                    $add2['user_id'] = $user2->user_id;
                    $add2['user_name'] = $user2->user_name;
                    $add2['is_pgc_author'] = $user2->is_pgc_author;
                    $add2['user_profile_image_url'] = $user2->user_profile_image_url;
                    $add2['text'] = $user2->text;
                    $add2['add_time'] = $time;
                    $add2['offset'] = $offset;

                    // 判断用户是否已存在
                    $id = $tt->where('text',$add2['text'])->column('id');

                    if(!$id){
                        // 获取其他信息
                        $url = 'http://isub.snssdk.com/2/user/profile/v2/?user_id='.$add['user_id'].'&iid=18415669017&device_id=41915130930&ac=wifi&channel=jiawo2&aid=13&app_name=news_article&version_code=499&version_name=4.9.9&device_platform=android&ssmix=a&device_type=GT-I9060C&os_api=19&os_version=4.4.2&uuid=379650010925175&openudid=5c514fe4fcd77606&manifest_version_code=500';

                        $user4 =  $this->getData($url);
                        $user4 = $user4->data;
                        $add2['description'] = $user4->description;
                        $add2['bg_img_url'] = isset($user3->bg_img_url)?$user4->bg_img_url:'';
                        $add2['share_url'] = isset($user3->share_url)?$user4->share_url:'';
                        $add2['mobile'] = isset($user3->mobile)?$user4->mobile:'';

                        $more ++;
                        $tt->insert($add2);
                    }
                }
                $all ++;
            }


            $rt['status'] = 1;
            $rt['more'] = $more;
            $rt['offset'] = (int)$cu + $all;
        }else{
            $rt['status'] = 2;
        }
        return $rt;
    }

    /**
     * @desc               从头条爬取评论数据2
     * @time 2018/1/6 15:41
     * @author LTK 617209350@qq.com
     */
    public function getToutiaoList2()
    {
        return false;
        $cu = input('offset');
        $offset = (int)$cu;

        $url = "http://isub.snssdk.com/article/v1/tab_comments/?group_id=6507586637612974599&item_id=6507586637612974599&aggr_type=1&count=50&offset=".$offset."&tab_index=0&iid=18415669017&device_id=41915130930&ac=wifi&channel=jiawo2&aid=13&app_name=news_article&version_code=499&version_name=4.9.9&device_platform=android&ssmix=a&device_type=GT-I9060C&os_api=19&os_version=4.4.2&uuid=379650010925175&openudid=5c514fe4fcd77606&manifest_version_code=500r";
        $data = $this->getData($url);

        $time = time();
        $tt = Db::name('tt_user');

        // 对返回数据进行处理
        if(!empty($data->data)){
            $all = 0;
            $list = $data->data;

            foreach ($list as $k=>$v){
                $user = $v->comment;
                $add['user_id'] = $user->user_id;
                $add['reply_count'] = $user->reply_count;
                $add['user_name'] = $user->user_name;
                $add['score'] = $user->score;
                $add['is_pgc_author'] = $user->is_pgc_author;
                $add['user_profile_image_url'] = $user->user_profile_image_url;
                $add['text'] = $user->text;
                $add['create_time'] = $user->create_time;
                $add['add_time'] = $time;

                // 判断用户是否已存在
                $id = $tt->where('user_name',$add['user_name'])->column('id');

                if(!$id){
                    $tt->insert($add);
                }

                // 获取第一个评论的人
                if($v->comment->reply_list){
                    $user2 = $v->comment->reply_list[0];

                    $add2['user_id'] = $user2->user_id;
                    $add2['user_name'] = $user2->user_name;
                    $add2['is_pgc_author'] = $user2->is_pgc_author;
                    $add2['user_profile_image_url'] = $user2->user_profile_image_url;
                    $add2['text'] = $user2->text;
                    $add2['add_time'] = $time;

                    // 判断用户是否已存在
                    $id = $tt->where('user_name',$add2['user_name'])->column('id');

                    if(!$id){
                        $tt->insert($add2);
                    }
                }
                $all ++;
            }

            $rt['status'] = 1;
            $rt['offset'] = (int)$cu + $all;
        }else{
            $rt['status'] = 2;
        }
        return $rt;
    }

    /**
     * @desc               获取头条单身用户表
     * @return string
     * @time 2018/1/6 15:41
     * @author LTK 617209350@qq.com
     */
    public function getUserList()
    {
        // 关键词搜索
        $key_word = input('key_word');
        $arr = explode(',',$key_word);

        $condition = '';

        if($key_word){
            if(count($arr) == 1){
                $condition = 'text like '."'%$arr[0]%'";
            }else{
                foreach($arr as $k=>$v){
                    if($k == 0){
                        $condition .= ' text like '."'%$arr[0]%'";
                    }else{
                        $condition .= ' and text like '."'%$v%'";
                    }
                }
            }
        }

        $sort_order = 'id asc';
        $model = Db::name('tt_user_all');

        $userList = $model->where($condition)->order($sort_order)->limit((input('page') -1)*input('limit') .','.input('limit'))->select();

        foreach ($userList as $k=>$v){
            $userList[$k]['create_time'] = $v['create_time'] ? date('Y-m-d H:i:s',$v['create_time']) :'暂无时间';
        }

        $count = $model->where($condition)->count();

        $rt['code']  = 0;
        $rt['count']  = $count;
        $rt['data']  = $userList;

        return json($rt);
    }


    /**
     * @desc                获取网站数据
     * @param string $url          地址
     * @return mixed
     * @time 2018/1/6 15:40
     * @author LTK 617209350@qq.com
     */
    public function getData($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $data = json_decode($data);
        return $data;
    }
}
