<?php

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Exception\TransferException;
use DiDom\Document;
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

$router->get('/domains', ['as' => 'domains.index', function () {
    $domains = Domain::paginate(10);
    return view('domains.index', [
        'domains' => $domains
    ]);
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
    $url = $request->input('url');
    try {
        $response = app()->make('Guzzle')->request('GET', $url);
    } catch (TransferException $e) {
        return view('index', [
            'errors' => [
                'url' => ['Bad URL or transfer error.']
            ]
        ]);
    }
    $body = $response->getBody()->getContents();
    if (array_key_exists(0, $response->getHeader('content-length'))) {
        $contentLength = (int) $response->getHeader('content-length')[0];
    } else {
        $contentLength = strlen($body);
    }
    $responseCode = $response->getStatusCode();
    $document = new Document($body);
    $h1 = $document->first('h1');
    $documentHeader = $h1 ? $h1->text() : '';
    $meta1 = $document->first('meta[name=keywords]');
    $keywords = $meta1 ? $meta1->getAttribute('content') : '';
    $meta2 = $document->first('meta[name=description]');
    $description = $meta2 ? $meta2->getAttribute('content') : '';
    $domain = Domain::create([
        'name' => $url,
        'content_length' => $contentLength,
        'response_code' => $responseCode,
        'body' => $body,
        'header' => $documentHeader,
        'keywords' => $keywords,
        'description' => $description
    ]);
    return redirect()->route('domains.show', ['id' => $domain->id]);
}]);

$router->get('/domains/{id}', ['as' => 'domains.show', function (int $id) {
    $domain = Domain::findOrFail($id);
    return view('domains.show', [
            'domain' => $domain
    ]);
}]);
