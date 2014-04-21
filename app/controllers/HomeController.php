<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/



    public function home()
    {
        View::share('todos', Todo::all());
        return View::make('home')->nest('listView', 'list');
    }

    public function deleteTodo($id){
        $todo = Todo::where('id', $id);
        $response = array();
        if($todo){
            $todo->delete();
            $response['status'] = 200;
            $response['message'] = "success";
        } else {
            $response['status'] = 400;
            $response['message'] = "No Todo found with given identifier!";
        }

        return Response::json($response);
    }

    public function updateTodo(){
        $id = $_POST['id'];

        $response = array();

        if($id){
            $todo = Todo::find((int) $id);
            $response['mau'] = $todo;
            $todo->setDescription($_POST['description']);
            $todo->save();
            $response['status'] = 200;
            $response['message'] = "success";
            $response['description'] = $todo->getDescription();
        } else {
            $response['status'] = 400;
            $response['message'] = "No Todo found with given identifier!";
        }

        return Response::json($response);
    }

    public function createTodo(){
        $description = $_POST['description'];
        $response = array();

        $todo = new Todo();
        $todo->setDescription($description);
        $todo->save();

        $response['id'] = $todo->id;
        $response['description'] = $todo->getDescription();
        $response['code'] = 200;
        $response['message'] = "success";

        return Response::json($response);
    }
}
