<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/* Models */
use App\User;
use App\ResetPassword;

/* Requests */
use App\Http\Requests\ResetPassword\createpreResetPasswordRequest;
use App\Http\Requests\ResetPassword\createConfirmPINRequest;
use App\Http\Requests\ResetPassword\createResetPasswordRequest;

/* Mails */
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordEmail;


class ResetPassController extends Controller
{
    /**
     * 
     * UserController => Update account, delete account
     * 
     * ResetPassController  =>  
     *  
     * Reset Password 
     * 
     * user => forget password 
     * user => write email  
     * back => make sure the email is in DB 
     * back => Create a secret key  
     * back => send secret key to the user's email 
     * 
     * user => write the sec key 
     * back => Make sure that the sec key is true 
     * back => Make user can change his password 
     * user => write new password 
     * back => upadte user's password  
     * 
     * function pre-resetPassword(){
     * Receive an email
     * make sure the email is in DB
     * Create a secret key
     * send secret key to the user's email
     * }
     * function confirmPIN(){
     * write the sec key
     * Make sure that the sec key is true  
     * }
     * 
     * function resetPassword(){
     * Make user can change his password
     * write new password
     * upadte user's password
     * Delete sec key from DB
     * }
     * 
     * ['Note'] => You Must make the validation at each step
     * 
     */


    public function preResetPassword(createpreResetPasswordRequest $request)
    {
        
        //Receive an email
        //validate the input
        //Make sure the email is in DB
        $user = User::where('email',$request->email)->first();

        if(!$user){
            return response()->json(
                 ['error' => 'This is an email not found'],
                 401
             );
         }

        //Create a secret key
        $pin = rand(100000, 999999);

        //Save pin in DB
        $resetPassword = ResetPassword::create( [
            'id' => $user->id,
            'pin' => $pin
        ]);
        
        //send secret key to the user's email
        Mail::to($user)->send(new ResetPasswordEmail($resetPassword));
        
        //201 response
        return response()->json(
            ['message' => 'Check your email'],
            201
        );

    }

    public function confirmPIN(createConfirmPINRequest $request)
    {
        // Recive pin 
        // Validate the input
        // Make sure that the sec key is true 
        $ResetPassword = ResetPassword::where('pin',$request->pin)->first();
        if(!$ResetPassword){
            return response()->json(
                 ['error' => 'Wrong'],
                 401
             );
         }

        //201 response
        return response()->json(
            ['message' => 'Done'],
            201
        );

    }

    public function resetPassword(createResetPasswordRequest $request)
    {
        // Recive pin , new password
        // Validate the input
        // Make sure that the sec key is true 
        $ResetPassword = ResetPassword::where('pin',$request->pin)->first();

        if(!$ResetPassword){
            return response()->json(
                 ['error' => 'Wrong'],
                 401
             );
         }

        // upadte user's password
        $id = $ResetPassword->id;
        $newPassword = Hash::make($request->password);

        User::where('id', $id)->update(['password' => $newPassword]);

        // Delete sec key from DB
        DB::table('resetPasswords')->where('id', $id)->delete();

        //201 response
        return response()->json(
            ['message' => 'Password changed'],
            201
        );

    }



}
