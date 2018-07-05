<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use Validator;
use Redirect;
use Session;
use URL;
use Mail;
class LeadsController extends Controller 
{
	public function manageleads(){
	
		return view('manage-leads');
	}

	 public function autoComplete(Request $request) {        
        $term = Input::get('term');
        $products=DB::table('City_Master')->select('City_Name')
        ->where('City_Name', 'LIKE', '%'.$term.'%')
        ->take(5)->get();
        //print_r( $products);
        $data=array();
        foreach ($products as $product) {
                $data[]=array('value'=>$product->City_Name);
        }
        if(count($data)){
             // print_r($data);
             return $data;
         }
        else
            return ['value'=>'No Result Found'];
    }




     public function autoComplete_employee(Request $request) {
        
        $term = Input::get('term');
        $products=DB::table('employee_master')->select('Emp_Name')
        ->where('Emp_Name', 'LIKE', '%'.$term.'%')
        ->take(5)->get();
        //print_r( $products);
        $data=array();
        foreach ($products as $product) {
                $data[]=array('value'=>$product->Emp_Name);
        }
        if(count($data)){
             // print_r($data);
             return $data;
         }
        else
            return ['value'=>'No Result Found'];
    }



     public function autoComplete_vertical(Request $request) {
        
        $term = Input::get('term');
        $products=DB::table('Vertical_Master')->select('Vertical_Name')
        ->where('Vertical_Name', 'LIKE', '%'.$term.'%')
        ->take(5)->get();
        //print_r( $products);
        $data=array();
        foreach ($products as $product) {
                $data[]=array('value'=>$product->Vertical_Name);
        }
        if(count($data)){
             // print_r($data);
             return $data;
         }
        else
            return ['value'=>'No Result Found'];
    }


    public function autoComplete_Status(Request $request) {
        
        $term = Input::get('term');
        $products=DB::table('lead_status_master')->select('Lead_Status')
        ->where('Lead_Status', 'LIKE', '%'.$term.'%')
        ->take(5)->get();
        //print_r( $products);
        $data=array();
        foreach ($products as $product) {
                $data[]=array('value'=>$product->Lead_Status);
        }
        if(count($data)){
             // print_r($data);
             return $data;
         }
        else
            return ['value'=>'No Result Found'];
    }


    public function autoComplete_Source(Request $request) {
        
        $term = Input::get('term');
        $products=DB::table('source_master')->select('source_name')
        ->where('source_name', 'LIKE', '%'.$term.'%')
        ->take(5)->get();
        //print_r( $products);
        $data=array();
        foreach ($products as $product) {
                $data[]=array('value'=>$product->source_name);
        }
        if(count($data)){
             // print_r($data);
             return $data;
         }
        else
            return ['value'=>'No Result Found'];
    }




    public function leadreassigntelecaller()
    {
        $users=DB::select("call USP_dropdown_lead_City_Master()");
        $status=DB::select("call usp_get_leadstatus_master()");
        $source=DB::select("call usp_get_source_master()");
        $assign=DB::select("call usp_get_assign_master()");
        return view('lead-reassign-telecaller',['users'=>$users,'status'=>$status,'source'=>$source,'assign'=>$assign]);
    }


    public function leadleadcapture()
    {
        $vertical=DB::select("call usp_vertical_master()");
        $state=DB::select("call usp_get_statemaster()");
        $profess=DB::select("call usp_get_professionmaster()");       
        $city=DB::select("call usp_get_leadcity_master()");
        $assign=DB::select("call usp_get_assign_master()");
        $product=DB::select("call usp_get_product_master()");
        $source=DB::select("call usp_get_source_master()");
        $bank=DB::select("call usp_get_bankmaster()");
        $broker=DB::select("call usp_get_Broker_Master()");
        $status=DB::select("call usp_get_leadstatus_master()");
        // $city=DB::select("call usp_get_leadcity_master()");
        return view ('lead-lead-capture',['vertical'=>$vertical,'state'=>$state,'profess'=>$profess,'city'=>$city,'assign'=>$assign,'product'=>$product,'source'=>$source,'bank'=>$bank,'broker'=>$broker,'status'=>$status]);
    }


    public function lead_lead_capture(Request $req)
    {
        //print_r($req->all());exit();
        DB::statement("call Insert_lead_lead_capture(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",array($req->name,$req->Designation,$req->mobile,$req->pan,$req->email,$req->address,$req->Vertical_Id,$req->address1,$req->address2,$req->monthlyincome,$req->dob,$req->stateid,$req->cityid,$req->Lead_Status_id,$req->Pincode,$req->ndate,$req->campaignName,$req->lead_type,$req->profession,$req->productid,$req->emp_code,$req->loan_amt,$req->broker_id,$req->source_id,$req->Remark,$req->Bank_Id,$req->ExpctDisbsDate,$req->demo_given));
        return Redirect('lead-lead-capture');
    }


    public function prioritymanageleads()
    {
        return view('priority-manage-leads');
    }


    public function pbpayoutmaster()
    {
        return view('pb-payout-master');
    }

    public function pushsmsentry()
    {
        $smsdata=DB::select("call usp_get_assing_empmaster()");
        return view('push-sms-entry',['smsdata'=>$smsdata])->with('no', 1);
    } 



    public function getleaddata($fdate,$tdate)
    {
         
        $id=Session::get('empcode');
        $leaddata=DB::select("call Usp_get_lead_data('$fdate','$tdate','RB40000577')");          
        return json_encode($leaddata);
          
          
    }


    public function leadstatusupdateflow($id)
    {


        $user= DB::select("call usp_show_lead_data(?)",array($id))[0];
        //$user=DB::select("call usp_show_lead_data($id)");
        $city=DB::select("call usp_get_leadcity_master()");
        $source=DB::select("call usp_get_leadsource_master()");
        $broker=DB::select("call usp_get_leadbroker_master()");
        $bank=DB::select("call usp_get_leadbank_master()");
        $assign=DB::select("call usp_get_assign_master()");
        $mlstatus=DB::select("call usp_ml_status_master()");
        $manager=DB::select("call usp_get_lead_bmanager()");
        $rmanager=DB::select("call usp_get_lead_bmanager()");
        $product=DB::select("call usp_get_product_master()");
        return view('lead-status-update-flow',['city'=>$city,'source'=>$source,'broker'=>$broker,'bank'=>$bank,'assign'=>$assign,'user'=>$user,'mlstatus'=>$mlstatus,'manager'=>$manager,'rmanager'=>$rmanager,'product'=>$product]);
    }

    public function manage_leads(Request $req)
    {
        //print_r($req->all());exit();

         DB::select('call Update_Manage_lead_data(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',array($req->Lead_id,$req->name,$req->Designation,$req->mobile,$req->pan,$req->email,$req->address,$req->dob,$req->cityid,$req->Lead_Status_id,$req->Pincode,$req->ndate,$req->lead_type,$req->profession,$req->productid,$req->emp_code,$req->loan_amt,$req->broker_id,$req->source_id,$req->Remark,$req->Bank_Id,$req->ExpctDisbsDate,$req->demo_given,$req->AadharNo,$req->CompanyName,$req->Is_share,$req->sharePercent,$req->FollowTime,$req->Call_type,$req->lead_assigned,$req->Uploaded_Dt,$req->Bank_RM_Name,$req->Bank_RM_NO,$req->Bank_Login_Id,$req->Loan_Acc_No,$req->Business_M,$req->Relationship_M));
         return Redirect('manage-leads');

    }

        public function  sub_fbastatus($id)
         {

            $id = $_GET['id'];
            return $id;
 
       }

    public function leadassigntelecaller()
    {
        $status=DB::select('call usp_get_assign_tele_status()');
        $source=DB::select('call usp_get_assign_tele_source()');
        $city=DB::select('call usp_get_assign_tele_city()');
        $assign=DB::select('call usp_get_assign_tele_assignemp()');
        return view('lead-assign-telecaller',['status'=>$status,'source'=>$source,'city'=>$city,'assign'=>$assign]);
    }


    public function managefbadata()
    {
        return view('manage-fba-data');
    }


     public function managefba($fdate,$tdate)
    {
         
        $id=Session::get('Emp_Code');
        $fbalead=DB::select("call Usp_get_fba_data('$fdate','$tdate','RB40000059')");          
        return json_encode($fbalead);
          
          
    }


    public function editfbadata($id)
    {
       $user=DB::select("call usp_show_fba_data(?)",array($id))[0];
       $fba_m=DB::select("call usp_get_fba_smaster()");
       return view('edit-fba-data',['user'=>$user,'fba_m'=>$fba_m]);
    }

    public function edit_fba_data(Request $req)
    {
        //print_r($req->all());exit();
        DB::select('call update_fba_data(?,?,?,?,?,?,?)',array($req->Broker_id,$req->Broker_Name,$req->PAN_No,$req->remark,$req->time,$req->date,$req->FBA_Status));
        return Redirect('manage-fba-data');
    }


    

}



