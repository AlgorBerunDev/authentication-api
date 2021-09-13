<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account\Accounts;
use App\Models\Account\AccountPhones;
use App\Models\Account\Addresses;
use App\Models\Account\Schedules;
use App\Models\Account\UserAccounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ValidatorException;
use App\Exceptions\AccessDenied;
use App\Exceptions\Limitted;
use App\Exceptions\NotFoundOrRemoved;
use Illuminate\Validation\Rule;
use Storage;

class AccountController extends Controller
{
    public function getPhones(Request $request) {
        $phones = AccountPhones::all();

        return response()->json([
            'data' => $phones,
            'error' => 0
        ]);
    }
    public function getPhonesByAccountId(Request $request, $account_id) {
        $phones = AccountPhones::where('account_id', $account_id)->get();

        return response()->json([
            'data' => $phones,
            'error' => 0
        ]);
    }
    public function getPhoneById(Request $request, $id) {
        $phones = AccountPhones::where('id', $id)->first();

        return response()->json([
            'data' => $phones,
            'error' => 0
        ]);
    }
    public function addPhone(Request $request) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer|min:1',
            'phone' => 'required|regex:/[0-9]{12,17}/',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }

        $phone = AccountPhones::create($request->only(['account_id', 'phone']));

        return response()->json([
            'data' => $phone,
            'error' => 0
        ]);
    }

    public function updatePhone(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer|min:1',
            'phone' => 'required|regex:/[0-9]{12,17}/',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }

        $n = AccountPhones::where('id', $id)->update($request->only(['account_id', 'phone']));

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }

    public function deletePhone(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer|min:1',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }

        $n = AccountPhones::where('id', $id)->delete();

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }

    public function getAddresses(Request $request) {
        $addresses = Addresses::all();

        return response()->json([
            'data' => $addresses,
            'error' => 0
        ]);
    }
    public function getAddressesByAccountId(Request $request, $account_id) {

        $addresses = Addresses::where('account_id', $account_id)->get();

        return response()->json([
            'data' => $addresses,
            'error' => 0
        ]);

    }
    public function getAddressById(Request $request, $id) {
        $addresses = Addresses::where('id', $id)->first();

        return response()->json([
            'data' => $addresses,
            'error' => 0
        ]);
    }
    public function addAddress(Request $request) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;



        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer|min:1',
            'name' => 'required|string',
            'lat' => 'string',
            'lon' => 'string',
            'address' => 'required|string',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }


        $address = Addresses::create($request->only(['account_id', 'name', 'lat', 'lon', 'address']));

        return response()->json([
            'data' => $address,
            'error' => 0
        ]);
    }
    public function updateAddress(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer|min:1',
            'name' => 'string',
            'lat' => 'string',
            'lon' => 'string',
            'address' => 'string',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }

        $n = Addresses::where('id', $id)->update($request->only(['account_id', 'name', 'lat', 'lon', 'address']));

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
    public function deleteAddress(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer|min:1',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }

        $n = Addresses::where('id', $id)->delete();

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
    public function getScheduleById(Request $request, $account_id) {

        $schedules = Schedules::where('account_id', $account_id)->first();

        return response()->json([
            'data' => $schedules,
            'error' => 0
        ]);
    }
    public function addSchedule(Request $request) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'account_id' => 'required|integer|min:1|unique:schedules',
            'monday_begin' => 'required|date_format:H:i',
            'monday_end' => 'required|date_format:H:i',
            'tuesday_begin' => 'required|date_format:H:i',
            'tuesday_end' => 'required|date_format:H:i',
            'wednesday_begin' => 'required|date_format:H:i',
            'wednesday_end' => 'required|date_format:H:i',
            'thursday_begin' => 'required|date_format:H:i',
            'thursday_end' => 'required|date_format:H:i',
            'friday_begin' => 'required|date_format:H:i',
            'friday_end' => 'required|date_format:H:i',
            'saturday_begin' => 'required|date_format:H:i',
            'saturday_end' => 'required|date_format:H:i',
            'sunday_begin' => 'required|date_format:H:i',
            'sunday_end' => 'required|date_format:H:i',
            'lunch_time_begin' => 'required|date_format:H:i',
            'lunch_time_end' => 'required|date_format:H:i'
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))
            ->where('role', 'owner')->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }

        $schedules = Schedules::create($request->only([
            'account_id',
            'monday_begin',
            'monday_end',
            'tuesday_begin',
            'tuesday_end',
            'wednesday_begin',
            'wednesday_end',
            'thursday_begin',
            'thursday_end',
            'friday_begin',
            'friday_end',
            'saturday_begin',
            'saturday_end',
            'sunday_begin',
            'sunday_end',
            'lunch_time_begin',
            'lunch_time_end',
        ]));

        return response()->json([
            'data' => $schedules,
            'error' => 0
        ]);
    }
    public function updateSchedule(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'account_id' => 'integer|min:1|unique:schedules',
            'monday_begin' => 'date_format:H:i',
            'monday_end' => 'date_format:H:i',
            'tuesday_begin' => 'date_format:H:i',
            'tuesday_end' => 'date_format:H:i',
            'wednesday_begin' => 'date_format:H:i',
            'wednesday_end' => 'date_format:H:i',
            'thursday_begin' => 'date_format:H:i',
            'thursday_end' => 'date_format:H:i',
            'friday_begin' => 'date_format:H:i',
            'friday_end' => 'date_format:H:i',
            'saturday_begin' => 'date_format:H:i',
            'saturday_end' => 'date_format:H:i',
            'sunday_begin' => 'date_format:H:i',
            'sunday_end' => 'date_format:H:i',
            'lunch_time_begin' => 'date_format:H:i',
            'lunch_time_end' => 'date_format:H:i'
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $schedule = Schedules::where('id', $id)->first();

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $schedule->account_id)
            ->where('role', 'owner')->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }

        $schedules = Schedules::where('id', $id)->update($request->only([
            'account_id',
            'monday_begin',
            'monday_end',
            'tuesday_begin',
            'tuesday_end',
            'wednesday_begin',
            'wednesday_end',
            'thursday_begin',
            'thursday_end',
            'friday_begin',
            'friday_end',
            'saturday_begin',
            'saturday_end',
            'sunday_begin',
            'sunday_end',
            'lunch_time_begin',
            'lunch_time_end',
        ]));

        return response()->json([
            'data' => $schedules,
            'error' => 0
        ]);
    }
    public function deleteSchedule(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $schedule = Schedules::where('id', $id)->first();

        try {

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $schedule->account_id)
            ->where('role', 'owner')->firstOrFail();

        } catch (\Throwable $th) {
            throw new AccessDenied();
        }

        $n = $schedule->delete();

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
    public function getAccountUsers(Request $request, $account_id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $user_accounts = UserAccounts::with('users')->where('account_id', $account_id)->get();

        return response()->json([
            'data' => $user_accounts,
            'error' => 0
        ]);
    }
    public function addUser(Request $request) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|min:0',
            'account_id' => [
                'required',
                'integer',
                Rule::unique('user_accounts')
                ->where('account_id',$request->input('account_id'))
                ->where('user_id',$request->input('user_id'))
            ],
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {
            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))
            ->where('role', 'owner')
            ->firstOrFail();
        } catch(\Throwable $th) {
            throw new AccessDenied();
        }

        $user_account = UserAccounts::create(array_merge($request->only(['user_id', 'account_id']), ['role' => 'manager']));

        return response()->json([
            'data' => $user_account,
            'error' => 0
        ]);
    }
    public function updateUser(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|min:0',
            'account_id' => [
                'required',
                'integer',
                Rule::unique('user_accounts')
                ->where('account_id',$request->input('account_id'))
                ->where('user_id',$request->input('user_id'))
            ],
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        try {
            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $request->input('account_id'))
            ->where('role', 'owner')
            ->firstOrFail();
        } catch(\Throwable $th) {
            throw new AccessDenied();
        }

        $n = UserAccounts::where('id', $id)->update(array_merge($request->only(['user_id', 'account_id'])));

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
    public function removeUser(Request $request, $id) {

        $user_id = $request->get('payload')->user_id;

        try {
            $user_account = UserAccounts::find($id);

            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $user_account->account_id)
            ->where('role', 'owner')
            ->firstOrFail();

        } catch(\Throwable $th) {
            throw new AccessDenied();
        }

        $n = UserAccounts::where('id', $id)->delete();

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
    public function getAccounts(Request $request) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $user_accounts = UserAccounts::with('accounts')->where('user_id', $user_id)->get();

        return response()->json([
            'data' => $user_accounts,
            'error' => 0
        ]);
    }
    public function getAccountById(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $user_accounts = UserAccounts::with('accounts')->where([
            ['user_id', $user_id],
            ['account_id', $id]
        ])->first();

        return response()->json([
            'data' => $user_accounts,
            'error' => 0
        ]);
    }
    public function addAccount(Request $request) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        $validator = Validator::make($request->all(), [
            'logo' => 'mimes:png,jpg,jpeg,svg|max:2048',
            'name' => 'required|string|min:3|max:256|unique:accounts',
            'founded_date' => 'required|string',
            'email' => 'required|email:rfc,dns|unique:accounts',
            'website' => 'string',
            'amount_of_workers' => 'integer|min:1',
            'country' => 'string',
            'city' => 'string',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $account = Accounts::create($request->all());
        if($request->hasfile('logo')) {

            $file = $request->file('logo');
            $name = time().'_'.$file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/accounts/logo', $name, 'public');

            $account->logo = "/".$filePath;
            $account->save();
        }

        $user_accounts = UserAccounts::create(['user_id' => $user_id, 'account_id' => $account->id]);

        return response()->json([
            'data' => $account,
            'error' => 0
        ]);
    }
    public function updateAccount(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        try {
            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $id)
            ->where('role', 'owner')
            ->firstOrFail();
        } catch(\Throwable $th) {
            throw new AccessDenied();
        }

        $validator = Validator::make($request->all(), [
            'logo' => 'mimes:png,jpg,jpeg,svg|max:2048',
            'name' => 'string|min:3|max:256|unique:accounts',
            'founded_date' => 'string',
            'email' => 'email:rfc,dns',
            'website' => 'string',
            'amount_of_workers' => 'integer|min:1',
            'country' => 'string',
            'city' => 'string',
        ]);

        if($validator->fails()){
            throw new ValidatorException($validator->messages());
        }

        $account = Accounts::where('id', $id)->first();

        if($request->hasfile('logo')) {

            $file = $request->file('logo');
            $name = time().'_'.$file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/accounts/logo', $name, 'public');

            if(Storage::exists("/public".$account->logo)){
                Storage::delete("/public".$account->logo);
            }

            $account->logo = "/".$filePath;
            $account->save();
        }

        $n = Accounts::where('id', $id)->update($request->only([
            'name',
            'founded_date',
            'email',
            'website',
            'amount_of_workers',
            'country',
            'city',
        ]));

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
    public function deleteAccount(Request $request, $id) {
        $session_id = $request->get('payload')->session_id;
        $user_id = $request->get('payload')->user_id;

        try {
            UserAccounts::where('user_id', $user_id)
            ->where('account_id', $id)
            ->where('role', 'owner')
            ->firstOrFail();
        } catch(\Throwable $th) {
            throw new AccessDenied();
        }

        $account = Accounts::where('id', $id)->first();

        if(Storage::exists("/public".$account->logo)){
            Storage::delete("/public".$account->logo);
        }

        $n = $account->delete();

        return response()->json([
            'data' => $n,
            'error' => 0
        ]);
    }
}
