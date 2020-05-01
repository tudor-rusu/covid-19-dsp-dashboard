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
}

//GRFWP8	Brown Peter	f9b6aOjF	Nadlac	27 04 2020 05:35:3920 04 2020 14:58:3323 04 2020 11:15:18
//GYHNP0	Kling Dillan	xK7IXuTN	Nadlac	27 04 2020 05:35:5321 04 2020 20:45:5323 04 2020 03:47:36
//NHGMB8	Gislason Larissa	mPPUGNRM	Nadlac	27 04 2020 05:35:4620 04 2020 21:33:2523 04 2020 16:23:20
//*****//O4D4KV	Hudson Erica	8czrEPTZ	Nadlac	27 04 2020 05:35:2524 04 2020 04:27:52
//0W7K02	White Aron	7SqXv9MA	Nadlac	27 04 2020 05:36:0422 04 2020 00:59:3325 04 2020 04:37:50
//XAILJV	Cruickshank Arlie	0IRAcKmp	Nadlac	27 04 2020 05:35:17
//BTEXJW	Hodkiewicz Leslie	nzXp79Zk	Nadlac	27 04 2020 05:36:0223 04 2020 05:31:3323 04 2020 09:55:48
//WROBGI
