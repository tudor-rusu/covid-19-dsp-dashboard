<?php

namespace App\Http\Controllers;

use App\Checkpoint;
use App\Declaration;
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
        $declarations = Declaration::all(Declaration::API_DECLARATION_URL(), ['page' => 1, 'per_page' => 10]);

        if(!is_array($declarations)) {
            session()->flash('type', 'danger');
            session()->flash('message', $declarations);
            $declarations = [];
        }

        $checkpoints = Checkpoint::all(Checkpoint::API_BORDER_URL(), ['status' => 'active']);

        if(!is_array($checkpoints)) {
            session()->flash('type', 'danger');
            session()->flash('message', $checkpoints);
            $checkpoints = [];
        }

        return view('home', ['checkpoints' => $checkpoints, 'declarations' => $declarations]);
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
}
