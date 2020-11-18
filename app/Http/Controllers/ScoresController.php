<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scores;
use Carbon\Carbon;
use DB;

class ScoresController extends Controller
{
    public function generate()
    {
    	$score = rand(1,10);
    	
    	$item = new Scores;
    	$item->score = $score;
    	$item->save();

    	if ($item) {
    		return response()->json([
                'data' => [
                    'id' => $item->id,
                    'score' => $item->score,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s')
                ]
            ], 201);
    	} else {
    		return response()->json([
                'error' => 1,
                'message' => 'Fail'
            ], 400);
    	}
    }

    public function show()
    {
     	$scores = Scores::select('id','score','created_at')->get();

		return response()->json([
		      'data' => $scores
		    ], 200);
    }

    public function getScoresAnalytics()
    {
     	$now = Carbon::now();
		$weekStartDate = $now->startOfWeek()->format('Y-m-d H:i:s');
		$weekEndDate = $now->endOfWeek()->format('Y-m-d H:i:s');

		$data = Scores::whereBetween( 'created_at',[$weekStartDate,$weekEndDate])
             ->groupBy( 'date' )
             ->orderBy( 'date' )
             ->get( [
                 DB::raw( 'DAYNAME( created_at ) as date' ),
                 DB::raw( 'COUNT( * ) as "count"' )
             ] )
             ->pluck( 'count', 'date' );

		return response()->json([
		      'data' => $data
		    ], 200);
    }

	public function getScoreById($scoreId)
	{
		$item = Scores::find($scoreId);
		if ($item) {
			return response()->json([
			    'data' => [
                    'id' => $item->id,
                    'score' => $item->score,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s')
                ]
			], 201);
		} else {
			return response()->json([
	    		'message' => 'Not Found'
			], 404);
		}
	}


}
