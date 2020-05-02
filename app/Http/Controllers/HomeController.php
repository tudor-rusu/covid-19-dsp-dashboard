<?php

namespace App\Http\Controllers;

use App\Declaration;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use PeterColes\Countries\CountriesFacade;
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

        if(!is_array($declaration)) {
            session()->flash('type', 'danger');
            session()->flash('message', $declaration);
            $formatedDeclaration['declaration'] = [];
        } else {
            $formatedDeclaration = Declaration::getDeclationColectionFormated($declaration, $countries, app()->getLocale());
        }

        return view('declaration', [
            'declaration'   => $formatedDeclaration['declaration'],
            'pdfData'       => json_encode($formatedDeclaration['pdf_data']),
            'signature'     => $formatedDeclaration['signature'],
            'qrCode'        => $formatedDeclaration['qr_code']
        ]);
    }

    /**
     * Return the formated declarations list
     *
     * @return mixed
     * @throws Exception
     */
    public function list(Request $request)
    {
//        if (!$request->ajax()) {
//            abort(403);
//        }

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

    /**
     * Refresh list of declarations
     *
     * @param Request $request
     *
     * @return RedirectResponse|void
     */
    public function postRefreshList(Request $request)
    {
        if($request->input('refresh')) {
            Cache::forget('declarations-' . Auth::user()->username);
            return back();
        }

        return;
    }

    /**
     * Search and return a declaration
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postSearchDeclaration(Request $request)
    {
        try {
            if($request->input('code')) {
                $code = $request->input('code');
                $declaration = Declaration::find(Declaration::API_DECLARATION_URL(), $code);
                $errorsMessage = '';

                if(!is_array($declaration)) {
                    throw new Exception($declaration);
                }

                if (Auth::user()->checkpoint != $declaration['border_checkpoint']['id']) {
                    $errorsMessage .= __('app.The person chose another border checkpoint.') . ' ';
                }

                if ($declaration['border_crossed_at'] && !$declaration['border_validated_at']) {
                    $crossedAt = Carbon::parse($declaration['border_crossed_at'])->format('d m Y H:i:s');
                    $errorsMessage .= __('app.The person crossed border checkpoint at :crossedAt but was not validated yet.',
                            ['crossedAt' => $crossedAt]) . ' ';
                }

                if ($declaration['dsp_validated_at'] && $declaration['dsp_user_name'] !== Auth::user()->username) {
                    $dspValidatedAt = Carbon::parse($declaration['border_validated_at'])->format('d m Y H:i:s');
                    $errorsMessage .= __('app.The declaration was validated at :dspValidatedAt by another DSP user [:userName].',
                            [
                                'dspValidatedAt' => $dspValidatedAt,
                                'userName' => $declaration['dsp_user_name']
                            ]) . ' ';
                }

                if (strlen($errorsMessage) < 1) {
                    return response()->json([
                        'success' => $code
                    ]);
                } else {
                    throw new Exception(trim($errorsMessage));
                }
            } else {
                throw new Exception(__('app.There is no code sent.'));
            }
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Register declaration to the authenticated user
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postRegisterDeclaration(Request $request)
    {
        try {
            if($request->input('code')) {
                $code = $request->input('code');
                $userName = Auth::user()->username;
                $errorsMessage = '';

                $registerDeclaration = Declaration::registerDeclaration(
                    Declaration::API_DECLARATION_URL(), $code, $userName);

                if ($registerDeclaration !== 'success') {
                    $errorsMessage .= $registerDeclaration;
                }

                if (strlen($errorsMessage) < 1) {
                    return response()->json([
                        'success' => $registerDeclaration
                    ]);
                } else {
                    throw new Exception(trim($errorsMessage));
                }
            } else {
                throw new Exception(__('app.There is no code sent.'));
            }
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }
    }
}

//ACDX0N	Wiegand Rashad	NkSdSRUw	Nadlac	02 05 2020 05:52:4625 04 2020 10:29:2625 04 2020 12:34:45
//0O8XB6	Hane Nolan	XSBurXDQ	Nadlac	02 05 2020 05:52:3025 04 2020 15:25:1528 04 2020 02:35:01
//**//FJHMGU	Towne Bertha	vXWJgOPb	Nadlac	02 05 2020 05:51:0127 04 2020 12:48:19
//**//CNMZX5	Powlowski Abagail	2CHwl69s	Nadlac	02 05 2020 05:51:1130 04 2020 17:44:31
//4E0ZCH	Christiansen Juanita	5bILUAGU	Nadlac	02 05 2020 05:51:22
//IWAFCE	Homenick Julien	4zKlbOIf	Nadlac	02 05 2020 05:52:3730 04 2020 07:03:3325 04 2020 13:49:48
//SKKML0	Becker Triston	RsdRT1DQ	Nadlac	02 05 2020 05:51:0029 04 2020 08:41:00
//CARPOR	Murazik Vernie	CKKq2DRF	Nadlac	02 05 2020 05:51:1527 04 2020 00:24:1930 04 2020 01:59:55
//I9NO8G	Leannon Randall	AniDJcUx	Nadlac	02 05 2020 05:51:0827 04 2020 12:43:0727 04 2020 09:37:11
//GVJFXW
