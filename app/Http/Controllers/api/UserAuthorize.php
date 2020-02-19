<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class UserAuthorize extends Controller
{
    /**
     * 微信小程序用户登陆
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function wxMiniLogin(Request $request){
        $input = $request->all();

        $appid = 'wx9c95c56137e3b5f1';
        $appsecret = '413ffb72c5dac934fd750f6132486fea';
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$input['code'].'&grant_type=authorization_code';

        // curl发起请求
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置url属性
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);//获取数据
        curl_close($ch);//关闭curl
        $output_arr = json_decode($output,true);

        if(isset($output_arr['openid'])){
            // 如果授权成功，返参有openid，查找数据
            $result = User::where('openid',$output_arr['openid'])
                ->where('status' , '>=' ,1)
                ->orderBy('id','desc')
                ->first();

            if(empty($result)){
                $output_arr['status'] = 1;
                $output_arr['name'] = '游客';
                $output_arr['password'] = md5('ai123456');

                $result = User::create($output_arr);

                return response()->json(['errcode'=>0,'msg'=>'登陆成功.','data'=>['id'=>$result['id'],'openid'=>$output_arr['openid'],'status'=>$result['status']]]);

            } else {
                
                return response()->json(['errcode'=>0,'msg'=>'用户已登陆.','data'=>['id'=>$result['id'],'openid'=>$result['openid'],'status'=>$result['status']]]);

            }

        } else {
            return response()->json(['errcode'=>401,'msg'=>'微信接口请求失败，原因:'.$output_arr['errcode'].$output_arr['errmsg'],'data'=>[]]);
        }
    }

    /**
     * 用户信息写入
     * @param Request $request
     */
    public function infoInput(Request $request){
        $input = $request->all();

        $result = User::find($input['id'])->update($input);
        return response()->json(['errcode'=>0,'msg'=>'更新用户信息成功！','data' => $result]);

    }

    /**
     * 用户绑定或解绑
     * @param Request $request
     */
    public function bind(Request $request){
        $input = $request->all();

        if(empty($input['id'] || empty($input['relation_id'] || empty($input['action_type'])))){
            return response()->json(['errcode'=>402,'msg'=>'请检查必填字段是否为空','data' => []]);
        }

        if($input['action_type'] === 'bind'){
            $user_a = User::find($input['id']);
            $user_b = User::find($input['relation_id']);
            if($user_a->status == 1 && $user_b->status == 1){
                $user_a->update(['status'=>2,'relation_id' => $input['relation_id']]);
                $user_b->update(['status'=>2,'relation_id' => $input['id']]);
                return response()->json(['errcode'=>0,'msg'=>'绑定成功！','data' => []]);

            } else {
                return response()->json(['errcode'=>403,'msg'=>'用户状态不可绑定','data' => []]);
            }
        } elseif($input['action_type'] === 'unbind') {
            $user_a = User::find($input['id']);
            $user_b = User::find($user_a->relation_id);

            if ($user_a->status == 2 && $user_b->status == 2) {
                $user_a->update(['status'=>1,'relation_id' => NULL]);
                $user_b->update(['status'=>1,'relation_id' => NULL]);
                return response()->json(['errcode'=>0,'msg'=>'解绑成功！','data' => []]);
            } else {
                return response()->json(['errcode' => 403, 'msg' => '用户状态不可绑定', 'data' => []]);
            }

        } else {
            return response()->json(['errcode'=>403,'msg'=>'未知操作类型','data' => []]);

        }

    }

}
