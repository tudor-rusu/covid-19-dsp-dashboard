<?php

namespace App\Http\Controllers;

use App\Declaration;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the declaration.
     *
     * @param string $code
     *
     * @return Factory|View
     */
    public function show(string $code)
    {
        $declaration = Declaration::find(Declaration::API_DECLARATION_URL(), $code);

        if(!is_array($declaration)) {
            session()->flash('type', 'danger');
            session()->flash('message', $declaration);
            $declaration = [];
        }

//        $signature = Declaration::getSignature(Declaration::API_DECLARATION_URL(), $code);

//        if(!is_array($signature)) {
//            session()->flash('type', 'danger');
//            session()->flash('message', $signature);
//            $signature = [];
//        }

//        return view('declaration', ['declaration' => $declaration, 'signature' => $signature]);
        return view('declaration', ['declaration' => $declaration]);
    }

    /**
     * Return the formated declarations list
     *
     * @return mixed
     * @throws Exception
     */
    public function list()
    {
        $declarations = Declaration::all(
            Declaration::API_DECLARATION_URL(),
            ['page' => 1, 'per_page' => 1000000],
            'datatables'
        );

        if(!is_array($declarations)) {
            session()->flash('type', 'danger');
            session()->flash('message', $declarations);
            $declarations = [];
        }
//        dd($declarations);die;

        return datatables()->of($declarations)
            ->make(true);
    }
}
