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
        $pdfData = '';
        $signature = '';
        $qrCode = '';
        $addresses = [];
        $visitedCountries = [];

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
            $qrCode = 'data:image/png;base64,' . $declaration['qr_src'];
            if (count($declaration['isolation_addresses']) > 0) {
                if (app()->getLocale() === 'ro') {
                    foreach ($declaration['isolation_addresses'] as $key => $address) {
                        $declaration['isolation_addresses'][$key]['city_arrival_date'] = Carbon::createFromFormat('Y-m-d', $address['city_arrival_date'])
                            ->format('d m Y');
                        $declaration['isolation_addresses'][$key]['city_departure_date'] = Carbon::createFromFormat('Y-m-d', $address['city_departure_date'])
                            ->format('d m Y');
                    }
                }
                foreach ($declaration['isolation_addresses'] as $key => $address) {
                    $addresses[$key]['locality'] = $address['city'] . (($address['county'] && strlen
                            ($address['county']) > 0) ? ', ' . $address['county'] : '');
                    $addresses[$key]['dateArrival'] = $address['city_arrival_date'];
                    $addresses[$key]['dateLeave'] = $address['city_departure_date'];
                    $addresses[$key]['fullAddress'] = $address['city_full_address'];
                }
            }
            $declaration['fever'] = in_array('fever', $declaration['symptoms']) ?? true;
            $declaration['swallow'] = in_array('swallow', $declaration['symptoms']) ?? true;
            $declaration['breath'] = in_array('breath', $declaration['symptoms']) ?? true;
            $declaration['cough'] = in_array('cough', $declaration['symptoms']) ?? true;
            $declaration['itinerary'] = '';
            if (count($declaration['itinerary_country_list']) > 0) {
                foreach($declaration['itinerary_country_list'] as $country) {
                    $visitedCountries[] = $countries[$country];
                    $declaration['itinerary'] .= '<strong>' . $countries[$country] . '</strong>, ';
                }
                $declaration['itinerary'] = substr($declaration['itinerary'], 0, -2);
            }
            $declaration['border'] = '';
            if ($declaration['border_checkpoint'] && $declaration['border_checkpoint']['status'] === 'active') {
                $declaration['border'] = $declaration['border_checkpoint']['name'];
            }
            $declaration['current_date'] = (app()->getLocale() === 'ro') ? Carbon::now()->format('d m Y') :
                Carbon::now()->format('m/d/Y');
            $pdfData = [
                'code' => $declaration['code'],
                'locale' => app()->getLocale(),
                'lastName' => $declaration['name'],
                'firstName' => $declaration['surname'],
                'sex' => $declaration['sex'],
                'idCardSeries' => $declaration['document_series'],
                'idCardNumber' => $declaration['document_number'],
                'birthday' => $declaration['birth_date'],
                'dateArrival' => $declaration['current_date'],
                'countryLeave' => $declaration['travelling_from_country'],
                'localityLeave' => $declaration['travelling_from_city'],
                'dateLeave' => $declaration['travelling_from_date'],
                'phoneNumber' => $declaration['phone'],
                'emailAddress' => $declaration['email'],
                'addresses' => $addresses,
                'answers' => [
                    'hasVisited' => $declaration['q_visited'],
                    'hasContacted' => $declaration['q_contacted'],
                    'isHospitalized' => $declaration['q_hospitalized'],
                    'hasFever' => $declaration['fever'],
                    'hasDifficultySwallow' => $declaration['swallow'],
                    'hasDifficultyBreath' => $declaration['breath'],
                    'hasIntenseCough' => $declaration['cough'],
                ],
                'organization' => '',
                'visitedCountries' => $visitedCountries,
                'borderCrossingPoint' => $declaration['border'],
                'destination' => trim(str_replace("\n", ' ', $declaration['travel_route'])),
                'vehicle' => $declaration['vehicle_registration_no'],
                'route' => '',
                'documentDate' => $declaration['current_date'],
                'documentLocality' => $declaration['border']
            ];
        }

        return view('declaration', [
            'declaration'   => $declaration,
            'pdfData'       => json_encode($pdfData),
            'signature'     => $signature,
            'qrCode'        => $qrCode
        ]);
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
