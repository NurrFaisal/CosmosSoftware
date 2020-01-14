<?php

namespace App\Http\Controllers;

use App\model\Package;
use App\PackageDay;
use Illuminate\Http\Request;
use Session;

class PackageController extends Controller
{
    protected function addPackageQueryValidation($request){
        $request->validate([
            'guest' => 'required',
            'package_type' => 'required',
            'country' => 'required',
            'query_date' => 'required',
            'destination' => 'required',
            'duration' => 'required',
            'adult_qty' => 'required',
            'total_qty' => 'required',
            'hotel_cat' => 'required',
            'room_qty' => 'required',
            'room_cat' => 'required',

            'package_days*day' => 'required',
            'package_days*day_date' => 'required',
            'package_days*over_night' => 'required',
            'package_days*tour_itinerary' => 'required',
            'package_days*breakfast' => 'required',
            'package_days*lunch' => 'required',
            'package_days*dinner' => 'required',

            'narration' => 'required',

        ]);
    }
    protected function addPackageQueryBasic($request, $package){
        $package->guest = $request->guest;
        $package->package_type = $request->package_type;
        $package->country = $request->country;
        $package->query_date = $request->query_date;
        $package->destination = $request->destination;
        $package->duration = $request->duration;
        $package->adult_qty = $request->adult_qty;
        $package->child_qty = $request->child_qty;
        $package->infant_qty = $request->infant_qty;
        $package->total_qty = $request->total_qty;
        $package->hotel_cat = $request->hotel_cat;
        $package->room_qty = $request->room_qty;
        $package->room_cat = $request->room_cat;
        $package->king_size = $request->king_size;
        $package->couple_size = $request->couple_size;
        $package->twin_size = $request->twin_size;
        $package->triple_size = $request->triple_size;
        $package->quared_size = $request->quared_size;
        $package->total_bed_qty = $request->total_bed_qty;
        $package->narration = $request->narration;
    }
    protected function packageDay($request, $package){
        $package_day_arrys = $request->package_days;
        $package_day_arrys_count = count($package_day_arrys);
        for ($i=0; $i<$package_day_arrys_count; $i++){
            $package_day = new PackageDay();
            $package_day->package_id = $package->id;
            $package_day->day = $package_day_arrys[$i]['day'];
            $package_day->day_date = $package_day_arrys[$i]['day_date'];
            $package_day->over_night = $package_day_arrys[$i]['over_night'];
            $package_day->tour_itinerary = $package_day_arrys[$i]['tour_itinerary'];
            $package_day->breakfast = $package_day_arrys[$i]['breakfast'];
            $package_day->lunch = $package_day_arrys[$i]['lunch'];
            $package_day->dinner = $package_day_arrys[$i]['dinner'];
            $package_day->save();
        }
    }

    public function addPackageQuery(Request $request){
       $this->addPackageQueryValidation($request);
       $package = new Package();
       $package->staff = Session::get('staff_id');
       $package->state = 1;
       $this->addPackageQueryBasic($request, $package);
       $package->save();
       $this->packageDay($request, $package);

       return 'New Package Query Added Successfully';
    }

    public function getAllPackageQuery(){
        $package_query = Package::with(['guestt' => function($q){$q->select('id','name', 'phone_number');}])->select('id','guest', 'country', 'destination', 'duration')->where('state', 1)->orderBy('id', 'desc')->paginate(10);
        return response()->json([
            'package_query' => $package_query
        ]);
    }
    public function editPackageQuery($id){
        $package_query = Package::with(['package_days', 'guestt' => function($q){$q->select('id','name', 'phone_number');}])->where('id', $id)->first();
        return response()->json([
            'package_query' => $package_query
        ]);
    }
    public function updatePackageQuery(Request $request){
        $this->addPackageQueryValidation($request);
        $package = Package::where('id', $request->id)->first();
        $this->addPackageQueryBasic($request, $package);
        $package->update();
        $package_days = PackageDay::where('package_id', $request->id)->get();
        foreach ($package_days as $package_day){
            $package_day->delete();
        }
        $this->packageDay($request, $package);
        return 'Package Query Updated Successfully';
    }
}
