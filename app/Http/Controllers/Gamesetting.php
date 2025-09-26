<?php
                    


namespace App\Http\Controllers;

use App\Models\Gameresult;
use App\Models\Setting;
use App\Models\Userbit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Gamesetting extends Controller
{
    
    public function crash_plane()
    {
        return 1;
    }
    public function game_existence(Request $r)
    {
        $event = $r->event;
        if ($event == "check") {
            $new = Setting::where('category', 'game_status')->where('value', '0')->first();
            
            if ($new || (session()->has('gamegenerate') && session()->get('gamegenerate') == 1)) {
                return array('data'=>true);
            }else{
                return array('data'=>false);
            }
            return array('data'=>false);
        }
    }
    public function new_game_generated(Request $r)
    {
        $new = Setting::where('category', 'game_status')->update(['value' => '0']);
        $r->session()->put('gamegenerate','1');
        return response()->json(array("id" => currentid()));
    }
    
    public function increamentor(Request $r)
    {


        $gamestatusdata = Setting::where('category', 'game_status')->first();
        $res = 0;
        if($gamestatusdata){
                
        $totalbet = Userbit::where('gameid',currentid())->count();
        $totalamount = Userbit::where('gameid',currentid())->sum('amount');
        if ($totalbet == 0) {
            $res =   rand(8,11);
        }else{
         //   $randomresult = array(1.1,1.1,1.2,1.3,1.4,1.5,1.6,1.7,1.8,1.9);
            
 $emailvalue = Setting::where('id', '14')->sum('value');

  $res =$emailvalue;
            
//           //  $gamestatusdataend = Setting::where('category', 'game_between_time_end')->first();
       //   $res =  $randomresult[rand(0,2)]; //$emailvalue; 
            
        }
        
                $status = true;
                $result = $res;
                $response = array('status'=>$status,'result'=>$result);
        return response()->json($response);
        }
    }
    // public function increamentor(Request $r)
    // {
    //     // return 1.7;
    //     $totalbet = Userbit::where('gameid',currentid())->count();
    //     $totalamount = Userbit::where('gameid',currentid())->sum('amount');
    //     if ($totalbet == 0) {
    //         return rand(8,11);
    //     }else{
    //         $randomresult = array(1.1,1.1,1.2,1.3,1.4,1.5,1.6,1.7,1.8,1.9);
    //         $res = $randomresult[rand(0,8)];
    //         if (session()->has('result')) {
    //             return session()->get('result');
    //         }
    //         $r->session()->put('result',$res);
    //         return $res;
    //     }
    //     return rand(setting('start_range_game_timer')*10, setting('end_range_game_timer')*10) / 10;
    // }
    
    public function game_over(Request $r)
    {
        $r->session()->forget('result');
        $result = Gameresult::where('id', currentid())->update([
            "result" => number_format($r->last_time, 2),
        ]);
        $alluserbit = Userbit::where('gameid', currentid())->where('status', 0)->get();
        foreach ($alluserbit as $key) {
			if(floatval($r->last_time) <= 1.20){
			$result = 0;
		    }else{
			$result = $r->last_time;
			}
            $finalamount = floatval($key->amount) * floatval($result);
            Userbit::where('id', $key->id)->update(["status"=> 1]);
            // addwallet($key->userid,$finalamount);
        }
        $new = Setting::where('category', 'game_status')->update(['value' => '0']);
        $r->session()->put('gamegenerate','0');
        $result = new Gameresult;
        $result->result = "pending";
        $result->save();
        return wallet(user('id'));
    }

    public function betNow(Request $r)
    {
        $status = false;
        $message = "Something went wrong!";
        $returnbets = array();
        for($i=0; $i < count($r->all_bets); $i++){
		$result = new Userbit;
        $result->userid = user('id');
        $result->amount = $r->all_bets[$i]['bet_amount'];
        $result->type = $r->all_bets[$i]['bet_type'];
        $result->gameid = currentid();
        $result->section_no = $r->all_bets[$i]['section_no'];
        if ($r->all_bets[$i]['bet_amount'] < wallet(user('id'), 'num')) {
            if ($result->save()) {
                $status = true;
                array_push($returnbets, [
                    "bet_id" => $result->id,
                ]);
				/*array_push($returnbets, [
                    "bet_id" => currentid(),
                ]);*/
                $exact_wallet_balance = addwallet(user('id'), floatval($r->all_bets[$i]['bet_amount']), "-");
                $data = array(
                    "wallet_balance" => wallet(user('id')),
                    "return_bets" => $returnbets
                );
                $message = "";
            }
        } else {
            $status = false;
            $data = array();
            $message = "Insufficient fund!!";
        }
		}
        $response = array("isSuccess" => $status, "data" => $data, "message" => $message);
        return response()->json($response);
    }
    public function currentlybet()
    {
        $allbets = Userbit::where("gameid", currentid())->join('users','users.id','=','userbits.userid')->get();
        $currentGameBet = $allbets;
        for ($i=0; $i < rand(400,900); $i++) { 
            $currentGameBet[]=array(
                "userid" => rand(10000,50000),
                "amount" => rand(999,9999),
				"image"  => "/images/avtar/av-".rand(1,72).".png"
            );
        }
        $currentGame = array("id"=>currentid());
        $currentGameBetCount = count($currentGameBet);
        $response = array("currentGame" => $currentGame, "currentGameBet" => $currentGameBet, "currentGameBetCount" => $currentGameBetCount);
        return response()->json($response);
    }
    public function my_bets_history(){
        $userid = user('id');
        $userbets = Userbit::where("userid", $userid)->where('status',1)->where('created_at', '>=', Carbon::today()->toDateString())->orderBy('id','desc')->get();
        return response()->json($userbets);
    }
public function cashout(Request $r)
{
    \Log::info('=== CASHOUT ATTEMPT ===', $r->all());
    
    try {
        $userid = user('id');
        $game_id = $r->game_id;
        $bet_id = $r->bet_id;
        $win_multiplier = $r->win_multiplier;
        
        // Validate inputs
        if (!$userid || !$game_id || !$bet_id || !$win_multiplier) {
            return response()->json([
                "isSuccess" => false, 
                "message" => "Missing required parameters"
            ]);
        }
        
        // Start database transaction
        \DB::beginTransaction();
        
        // Get the bet with lock to prevent double cashout
        $bet = \DB::table('userbits')
            ->where('id', $bet_id)
            ->where('userid', $userid)
            ->where('status', 0) // Only active bets
            ->lockForUpdate()
            ->first();
            
        if (!$bet) {
            \DB::rollBack();
            \Log::error('Cashout failed: Bet not found or already cashed out', [
                'user_id' => $userid,
                'bet_id' => $bet_id
            ]);
            return response()->json([
                "isSuccess" => false, 
                "message" => "Bet not found or already cashed out"
            ]);
        }
        
        // Calculate win amount
        $cash_out_amount = floatval($bet->amount) * floatval($win_multiplier);
        
        \Log::info('Cashout calculation', [
            'bet_amount' => $bet->amount,
            'multiplier' => $win_multiplier,
            'win_amount' => $cash_out_amount
        ]);
        
        // Update bet status and cashout multiplier
        $updateBet = \DB::table('userbits')
            ->where('id', $bet_id)
            ->update([
                "status" => 1,
                "cashout_multiplier" => $win_multiplier,
                "updated_at" => now()
            ]);
            
        if (!$updateBet) {
            \DB::rollBack();
            \Log::error('Cashout failed: Could not update bet status');
            return response()->json([
                "isSuccess" => false, 
                "message" => "Cashout failed - bet update error"
            ]);
        }
        
        // Add winnings to wallet
        $walletUpdated = addwallet($userid, $cash_out_amount);
        
        if (!$walletUpdated) {
            \DB::rollBack();
            \Log::error('Cashout failed: Could not update wallet');
            return response()->json([
                "isSuccess" => false, 
                "message" => "Cashout failed - wallet update error"
            ]);
        }
        
        // Record transaction
        \DB::table('transactions')->insert([
            'userid' => $userid,
            'type' => 'credit',
            'amount' => $cash_out_amount,
            'category' => 'game_win',
            'remark' => 'Cashout win - Bet ID: ' . $bet_id . ' at ' . $win_multiplier . 'x',
            'status' => '1',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        \DB::commit();
        
        \Log::info('Cashout successful', [
            'user_id' => $userid,
            'bet_id' => $bet_id,
            'win_amount' => $cash_out_amount,
            'multiplier' => $win_multiplier
        ]);
        
        $data = array(
            "wallet_balance" => wallet($userid, "num"),
            "cash_out_amount" => $cash_out_amount
        );
        
        return response()->json([
            "isSuccess" => true, 
            "data" => $data, 
            "message" => "Cashout successful! +" . $cash_out_amount . " TZS"
        ]);
        
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Cashout Exception', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            "isSuccess" => false, 
            "message" => "Cashout failed: " . $e->getMessage()
        ]);
    }
}	
	public function cronjob(){
	    //0 = Game end & statrting soon
	    //1 = Game start & and is in proccess
	    $gamestatusdata = Setting::where('category', 'game_status')->first();
	    $game_status = 0;
	    if($gamestatusdata){
	        $game_status = $gamestatusdata->value;
	    }
	    if($game_status == 1){
	    $last_start_time = Setting::where('category', 'game_start_time')->first()->value;
	    $last_till_time = Setting::where('category', 'game_between_time')->first()->value;
	    $bothdifference = datealgebra($last_start_time, '+', ($last_till_time/1000).' seconds', $format = "Y-m-d h:i:s");
	    if(strtotime(date('Y-m-d h:i:s')) >= strtotime($bothdifference)){
	        $gamestatusdata = Setting::where('category', 'game_status')->update([
	             "value"  => 0
	             ]);
	    }
	    }elseif($game_status == 0){
	         $gamestatusdata = Setting::where('category', 'game_status')->update(["value"  => 1]);
	         $gamestatusdata = Setting::where('category', 'game_start_time')->update(["value"  => date('Y-m-d h:i:s')]);
	         $gamestatusdata = Setting::where('category', 'game_between_time')->update(["value"  => 5000]);
	    }else{}
	}
}























