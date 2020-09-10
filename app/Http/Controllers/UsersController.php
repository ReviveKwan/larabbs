<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UsersController extends Controller
{
    //show 视图方法 由于 show() 方法传参时声明了类型 —— Eloquent 模型 User，对应的变量名 $user 会匹配路由片段中的 {user}，这样，Laravel 会自动注入与请求 URI 中传入的 ID 对应的用户模型实例。
    // 此功能称为 『隐性路由模型绑定』，是『约定优于配置』设计范式的体现，同时满足以下两种情况，此功能即会自动启用：
    // 1). 路由声明时必须使用 Eloquent 模型的单数小写格式来作为 路由片段参数，User 对应 {user}：
    // 2). 控制器方法传参中必须包含对应的 Eloquent 模型类型 提示，并且是有序的：
    public function show(User $user)
    {
        //我们将用户对象变量 $user 通过 compact 方法转化为一个关联数组，并作为第二个参数传递给 view 方法，将变量数据传递到视图中。
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user, ImageUploadHandler $uploader)
    {
        $data = $request->all();

        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id);
            if($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }


}
