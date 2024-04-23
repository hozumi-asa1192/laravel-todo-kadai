<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Goal $goal)
    {
       $request->validate([
            'content'=>'required',
            'discription'=>'required',
       ]);

       $todo = new Todo();
       $todo->content = $request->input('content');
       $todo->discription = $request->input('discription');
       $todo->user_id = Auth::id();
       $todo->goal_id = $goal->id;
       $todo->done = false;
       $todo->save();

       $todo->tags()->sync($request->input('tag_ids'));

       return redirect()->route('goals.index');
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Goal $goal, Todo $todo)
    {
        $request->validate([
            'content'=>'required',
        ]);

        $todo->content = $request->input('content');
        $todo->discription = $request->input('discription');
        $todo->user_id = Auth::id();
        $todo->goal_id = $goal->id;
        // boolean()でその値をboolean形式に変える、第二引数の値は、第一引数の値がない場合の初期設定ができる。
        $todo->done = $request->boolean('done',$todo->done);
        $todo->save();

        // 「完了」と「未完了」の切り替えでないとき（通常の編集時）にのみタグを変更する
        if(!$request->has('done')){
            $todo->tags()->sync($request->input('tag_ids'));
        };

        return redirect()->route('goals.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Goal $goal, Todo $todo)
    {
        $todo->delete();

        return redirect()->route('goals.index');
    }
}
