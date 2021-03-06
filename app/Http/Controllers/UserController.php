<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Cache;
use App\CustomValidation;

class UserController  extends Controller
{
    public function userlogin_details(){
        return view('user-login-details');
    }


 public function userdetail_update($id){
            $user=DB::select("call sp_user_detail_view($id)");
        return view('user-login-details',['user'=>$user]);
        
    }





     


    public function userlogin_view(Request $req){
            $data=DB::select('call sp_user_login_details()');
        return view('user-details',['data'=>$data])->with('no',1);      
    }




public function user_detail_insert(CustomValidation $validator,Request $req){
   // print_r($req->all());

    $data = array();
    $error = array();
    $parameters['REQUEST'] = $req->all();
    $parameters['VALIDATIONS'] = array(
    'REQUIRED_VALIDATIONS'=>array('name'=>'Please Enter Employee Code','email'=>'Please Enter Employee Name','mobile'=>'Please Enter Mobile No'),
    'EMAIL_VALIDATIONS'=>array('email'=>'Please Enter Valid Email Address')
);
    //print_r($parameters);
    extract($validator->validate_email($parameters));
    extract($validator->validate_required($parameters));
    if(count($error) === 0){
    $user = Session::get('id');
    $ipaddress = $_SERVER['REMOTE_ADDR'];

   
    $query= DB::insert('call sp_insert_user_details(?,?,?,?,?)',array($req->name,$req->email,$req->mobile, $req->last_login_date,$req->Created_Date));
    //print_r($query);
    //exit();

    $success_msg = array('status'=>'success',"messege"=>"Your File Successfully Insert","redirectUrl"=>"user-details");
            echo json_encode($success_msg);
   } 
    else{
        echo json_encode($error);
    }
  
    }
  



    

       public function user_update(Request $req){
        //print_r($req->all());
        //exit();
        if($req->is_active == '0'){
            $is_active = 1;
        }else{
            $is_active = null;
        }
        $ipaddress = $_SERVER['REMOTE_ADDR'];
        $user = Session::get('id');
        $user=DB::update("call sp_update_user_master(?,?,?,?,?)",array($req->name,$req->email,$req->mobile,$req->last_login_date,$req->Created_Date));
        // return Redirect('employee-master-view');
         $success_msg = array('name'=>'success',"messege"=>"Your File Successfully Updated");
        echo json_encode($success_msg);
    }





}

