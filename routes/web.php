<?php

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Domain;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', ['as' => 'index', function () {
    return view('index');
}]);

$router->post('/domains', ['as' => 'domains.store', function (Request $request) {
    try {
        $this->validate($request, [
            'url' => 'required'
        ]);
    } catch (ValidationException $e) {
        return view('index', [
            'errors' => $e->errors()
        ]);
    }
    $domain = Domain::create(['name' => $request->input('url')]);
    return redirect()->route('domains.show', ['id' => $domain->id]);
}]);

$router->get('/domains/{id}', ['as' => 'domains.show', function (int $id) {
    if ($domain = Domain::find($id)) {
        return view('domains.show', [
            'domain' => $domain->toArray()
        ]);
    }
    return abort(404);
}]);
