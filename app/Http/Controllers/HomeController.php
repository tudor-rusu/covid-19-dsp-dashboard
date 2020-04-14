<?php

namespace App\Http\Controllers;

use App\Declaration;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PeterColes\Countries\CountriesFacade;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
     * @param string  $code
     * @param Request $request
     *
     * @return Factory|View
     */
    public function show(string $code, Request $request)
    {
        if ($request->session()->has('language')) {
            app()->setLocale($request->session()->get('language'));
            $countries = CountriesFacade::lookup($request->session()->get('language'));
        } else {
            $countries = CountriesFacade::lookup('ro_RO');
        }

        $declaration = Declaration::find(Declaration::API_DECLARATION_URL(), $code);
        $signature = '';

        if(!is_array($declaration)) {
            session()->flash('type', 'danger');
            session()->flash('message', $declaration);
            $declaration = [];
        } else {
            if($declaration['signed']) {
                $signature = Declaration::getSignature(Declaration::API_DECLARATION_URL(), $code);

                if(is_array($signature)) {
                    if ($signature['status'] === 'success') {
                        $signature = $signature['signature'];
                    } else {
                        $signature = $signature['message'];
                    }
                } else {
                    session()->flash('type', 'danger');
                    session()->flash('message', $signature);
                    $signature = '';
                }
            }
            $declaration['travelling_from_country'] = $countries[$declaration['travelling_from_country_code']];
            if (app()->getLocale() === 'ro') {
                $declaration['travelling_from_date'] = Carbon::createFromFormat('Y-m-d', $declaration['travelling_from_date'])
                    ->format('d m Y');
            }
            $declaration['birth_date'] = Carbon::createFromFormat('Y-m-d', $declaration['birth_date'])
                ->format('d/m/Y');
            $declaration['qr_src'] = base64_encode(QrCode::format('png')->size(100)->generate($declaration['code']));
            if (count($declaration['isolation_addresses']) > 0) {
                if (app()->getLocale() === 'ro') {
                    foreach ($declaration['isolation_addresses'] as $key => $address) {
                        $declaration['isolation_addresses'][$key]['city_arrival_date'] = Carbon::createFromFormat('Y-m-d', $address['city_arrival_date'])
                            ->format('d m Y');
                        $declaration['isolation_addresses'][$key]['city_departure_date'] = Carbon::createFromFormat('Y-m-d', $address['city_departure_date'])
                            ->format('d m Y');
                    }
                }
            }
            $declaration['fever'] = in_array('fever', $declaration['symptoms']) ?? true;
            $declaration['swallow'] = in_array('swallow', $declaration['symptoms']) ?? true;
            $declaration['breath'] = in_array('breath', $declaration['symptoms']) ?? true;
            $declaration['cough'] = in_array('cough', $declaration['symptoms']) ?? true;
        }

        return view('declaration', ['declaration' => $declaration, 'signature' => $signature]);
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

        return datatables()->of($declarations)
            ->make(true);
    }

    /**
     * Change language
     *
     * @param Request $request
     *
     * @return RedirectResponse|void
     */
    public function postChangeLanguage(Request $request)
    {
        if($request->input('lang')) {
            $request->session()->put('language', $request->input('lang'));
            app()->setLocale($request->input('lang'));
            return back();
        }

        return;
    }
}
