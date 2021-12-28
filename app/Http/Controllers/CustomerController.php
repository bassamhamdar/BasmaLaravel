<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRegisterRequest;
use Illuminate\Support\Facades\Http;



class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        $today = Customer::whereDate('created_at', today())->count();
        $last24hour = Customer::where('created_at',">=",Carbon::now()->subHour(24))->where('created_at',"<",Carbon::now())->count();
        $lastMonth = Customer::whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->subMonth())->count();

        $last3month = Customer::whereYear('created_at', now()->year)
        ->whereMonth('created_at',">=", now()->subMonth(3))->count();

        $lastYear = Customer::whereYear('created_at', now()->year-1)->count();
        return response()->json([
            'today'=> $today,
            '24hr'=>$last24hour,
            'lastMn' => $lastMonth,
            'last3Mon'=>$last3month,
            'lastYear'=> $lastYear,
        ]);
    }


    public function fetch(Request $request){
        $paginationNumber = $request['paginationNumber'];
        $data = Customer::paginate($paginationNumber, ['id','name','email']);
        return response()->json([
            "status"=>200,
            "success"=> true,
            "message" => "Customers retreived successfully!",
            "data"=>$data,
        ]);
    }

    public function register(CustomerRegisterRequest $request)
    {     
        $inputs = $request->validated();
        $response = Http::asForm()->post("https://www.google.com/recaptcha/api/siteverify", [
            'secret' => '6LdJOp0dAAAAAAKcygoAcgFzcgM7MU6LrgOUB-fl',
            'response' => $request->captcha,
           
        ]);
        if( $response['success'] == true ){
            $customer = new Customer();
            $customer->fill($inputs);
            $customer->save();
            $token = auth()->login($customer);
    
            return response()->json([
                "status"=>200,
                "success"=> true,
                "message" => "you have been registered!",
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth()->factory()->getTTL() * 60
            ]);
        }else{
            return response()->json([
                "status"=>200,
                "success"=> false,
                "message" => "invalid recaptcha token",
            ]);
        }

    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
