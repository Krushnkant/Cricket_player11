<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Series;
use App\Models\Tournament;
use App\Models\SeriesTeam;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SeriesController extends Controller
{
    public function index(){
        $tournaments = Tournament::where('estatus',1)->get();
        $teams = Team::where('estatus',1)->get();
        return view('admin.series.list',compact('tournaments','teams'));
    }

    public function addorupdateseries(Request $request){
        //dd($request->all());
        $messages = [
            'name.required' =>'Please provide a name',
            'tournament_id.required' =>'Please provide a tournament',
            'series_type.required' =>'Please provide a series type',
            'start_date.required' =>'Please provide a start date',
            'end_date.required' =>'Please provide a end date',
        ];

        $validator = Validator::make($request->all(), [
            'tournament_id' => 'required',
            'name' => 'required',
            'series_type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        
        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $series = Series::find($request->series_id);
            if(!$series){
                return response()->json(['status' => '400']);
            }
           
            $series->name = $request->name;
            $series->tournament_id = $request->tournament_id;
            $series->series_type = $request->series_type;
            $series->start_date = $request->start_date;
            $series->end_date = $request->end_date;
        }else{
            $action = "add";
            $series = new Series();
            $series->name = $request->name;
            $series->tournament_id = $request->tournament_id;
            $series->series_type = $request->series_type;
            $series->start_date = $request->start_date;
            $series->end_date = $request->end_date;
            $series->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
           
        }

        $series->save();

        if($series){
            
            foreach($request->series_team as $team){
               $SeriesTeam = SeriesTeam::where('team_id',$team)->where('series_id',$series->id)->get();
               foreach($SeriesTeam as $STeam){
                    if(!in_array($STeam->team_id,$request->series_team)){
                        $seriesteam = SeriesTeam::find($STeam->id);
                        $seriesteam->delete(); 
                    }else{
                       
                    }
                }
            
                if(count($SeriesTeam) <= 0){
                    $seriesteam = New SeriesTeam();
                    $seriesteam->series_id = $series->id;
                    $seriesteam->team_id = $team;
                    $seriesteam->save();
                }

            }
        }
        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allseriesslist(Request $request){
        if ($request->ajax()) {
           

            $columns = array(
                0 =>'id',
                1 =>'name',
                2 =>'tournament',
                3 =>'series_type',
                4 =>'start_date',
                5 =>'end_date',
                6=> 'estatus',
                7=> 'created_at',
                8=> 'action',
            );

            $totalData = Series::count();

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            //dd($columns[$request->input('order.0.column')]);
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order == "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $seriess = Series::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            } else {
                $search = $request->input('search.value');
                $seriess =  Series::where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('name', 'LIKE',"%{$search}%")
                            ->orWhere('shot_name', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Series::count();
            }

            $data = array();

            if(!empty($seriess))
            {
                foreach ($seriess as $series)
                {
                    $page_id = ProjectPage::where('route_url','admin.series.list')->pluck('id')->first();

                    if( $series->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="seriesstatuscheck_'. $series->id .'" onchange="changeseriesStatus('. $series->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($series->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="seriesstatuscheck_'. $series->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $series->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="seriesstatuscheck_'. $series->id .'" onchange="changeseriesStatus('. $series->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($series->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="seriesstatuscheck_'. $series->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editseriesBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#seriesModel" onclick="" data-id="' .$series->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                        $action .= '<button id="viewSeriesTeamBtn" title="Series Team" class="btn btn-gray text-blue btn-sm" data-id="' .$series->id. '"><i class="fa fa-users" aria-hidden="true"></i></button>'; 

                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteseriesBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteseriesModel" onclick="" data-id="' .$series->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['name'] = $series->name;
                    $nestedData['tournament'] = $series->tournament->name;
                    $nestedData['series_type'] = matchType($series->series_type);
                    $nestedData['start_date'] = date('Y-m-d H:i:s', strtotime($series->start_date));
                    $nestedData['end_date'] = date('Y-m-d H:i:s', strtotime($series->end_date));
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($series->created_at));
                    $nestedData['action'] = $action;
                    $data[] = $nestedData;

                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        }
    }

    public function changeseriesStatus($id){
        $series = Series::find($id);
        if ($series->estatus==1){
            $series->estatus = 2;
            $series->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($series->estatus==2){
            $series->estatus = 1;
            $series->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function editseries($id){
        $data = array();
        $series = Series::find($id);
        $seriesteam = SeriesTeam::where('series_id',$series->id)->get()->pluck('team_id')->toArray();
        $data['series'] = $series;
        $data['seriesteam'] = $seriesteam;
        return response()->json($data);
    }

    public function deleteseries($id){
        $series = Series::find($id);
        if ($series){
            $series->estatus = 3;
            $series->save();
            $series->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function getsrno($id){
        $series = Series::where('tournament_id',$id)->orderBy('id','desc')->first();
        $sr_no = isset($series->sr_no)?$series->sr_no+1:1;
        return response()->json(['sr_no' => $sr_no]);
    }
}
