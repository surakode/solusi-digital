<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use LDAP\Result;
use Throwable;
use Carbon\CarbonPeriod;
use Carbon\Carbon;


class Helper
{
   public static function test($var)
   {
      return $var;
   }
   public static function LoginAction($id, $action)
   {
      if ($action == 'success') {
         $pdo = DB::getPdo();
         $query = 'UPDATE users SET last_login=NOW(), failed_login=0 WHERE id=:id';
         $stmt = $pdo->prepare($query);
         $stmt->bindValue(':id', $id);
         $stmt->execute();
         $pdo = null;
         return;
      } elseif ($action == 'failed') {
         // set failed_login = +1
         $pdo = DB::getPdo();
         $query = 'UPDATE users SET failed_login=failed_login+1 WHERE id=:id';
         $stmt = $pdo->prepare($query);
         $stmt->bindValue(':id', $id);
         $stmt->execute();
         $pdo = null;
         return;
      } else {
         $pdo = DB::getPdo();
         $query = 'SELECT failed_login FROM users WHERE id=:id';
         $stmt = $pdo->prepare($query);
         $stmt->bindValue(':id', $id);
         $stmt->execute();
         $data = $stmt->fetch($pdo::FETCH_ASSOC);
         $pdo = null;
         return $data['failed_login'];
      }
   }
   public static function Auth($user, $password, $type)
   {
      try {
         $pdo = DB::getPdo();
         if ($type == 'email') {
            $query = 'SELECT id,username,name,mobile,status,password FROM users WHERE email=:email';
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':email', $user);
         }
         if ($type == "mobile") {
            $query = 'SELECT id,username,name,mobile,status,password FROM users WHERE mobile=:mobile';
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':mobile', $user);
         }

         if ($type == "username") {
            $query = 'SELECT id,username,name,mobile,status,password FROM users WHERE username=:username';
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':username', $user);
         }
         $stmt->execute();
         $data = $stmt->fetch($pdo::FETCH_ASSOC);
         // dd($data);
         $pdo = null;
         if ((is_array($data) >= 1)) {
            // dd($data);

            $id = $data['id'];
            $hash_password = $data['password'];
            $counterLogin = Helper::LoginAction($id, '');
            if ($counterLogin >= env('MAX_FAILED_LOGIN')) {
               Helper::LoginAction($id, 'failed');
               $result = config('global.errors.E007');
               return $result;
            } else {
               if (Hash::check($password, $hash_password)) { //password benar?
                  if ($data['status'] == 0) { //user tidak aktif 0
                     $result = config('global.errors.E008');
                     return $result;
                  }
                  $result = config('global.success.S001');
                  return $result;
               } else { //password salah
                  Helper::LoginAction($id, 'failed');
                  $result = config('global.errors.E001');
                  return $result;
               }
            }
         }
         $result = config('global.errors.E001');
         return $result;
      } catch (\Throwable $th) {
         $result = config('global.errors.E999');
         $result = $th->getMessage();
         return $result;
      }
   }
   public static function title($title)
   {
      // $nameRoute = Str::title(Route::currentRouteName());
      $nameRoute = $title;
      $appName = config('app.name');
      $result = $nameRoute . ' • ' . $appName;
      if (empty($nameRoute)) {
         $result = $appName;
      }
      return $result;
   }

   public static function setDate($data, $type)
   {

      switch ($type) {
         case 'numMonth':
            $result = date('d-m-Y', strtotime($data));
            break;
         case 'strMonth':
            $result = date('d M Y', strtotime($data));
            break;
         case 'fullDate':
            $result = date('D, d M Y', strtotime($data));
            break;
         case 'bulan':
            $numMonth = intval(str_replace('0', '', $data));
            $month = array(
               1 => 'Januari',
               'Februari',
               'Maret',
               'April',
               'Mei',
               'Juni',
               'Juli',
               'Agustus',
               'September',
               'Oktober',
               'November',
               'Desember'
            );
            return $result = $month[$numMonth];
         case 'fullDateId':
            $month = array(
               1 =>   'Januari',
               'Februari',
               'Maret',
               'April',
               'Mei',
               'Juni',
               'Juli',
               'Agustus',
               'September',
               'Oktober',
               'November',
               'Desember'
            );
            $expld = explode('-', $data);

            // variabel expld 0 = tanggal
            // variabel expld 1 = bulan
            // variabel expld 2 = tahun

            return $expld[2] . ' ' . $month[(int)$expld[1]] . ' ' . $expld[0];
         default:
            $result = '';
            break;
      }
      return $result;
   }

   public static function checkACL($role, $permission)
   {
      $users_acl = Session::get('_users_acl');
      if (property_exists($users_acl, $role)) {
         $checkACL = strpos($users_acl->$role, $permission) !== false;
      } else {
         $checkACL = false;
      }

      // dd(strpos($users_acl->$role, $permission) !== false);
      return $checkACL;
   }
   public static function checkEditACL($role, $permission)
   {
      $checkACL = strpos($role, $permission) !== false;
      return $checkACL;
   }

   public static function forSelect($table, $columnID, $columnValue, $columnWhere, $columnWhereValue)
   {
      if (!$columnWhere) {
         $result =  DB::table($table)->select($columnID, $columnValue)->get();
      } else {
         $result =  DB::table($table)->select($columnID, $columnValue)->where($columnWhere, $columnWhereValue)->get();
      }

      return $result;
   }

   public static function trans($var)
   {
      if (array_key_exists($var, config('global.trans'))) {
         return config('global.trans.' . $var);
      }
      // else{
      //    return config('global.trans._valid');
      // }
   }

   public static function validationFail($validator)
   {
      if ($validator->fails()) {
         // dd( $validator);
         $messages = $validator->errors()->messages();
         $msg = "";
         foreach ($messages as $key => $message) {
            foreach ($message as $value) {
               $transKey = Helper::trans($key);
               $msg .= (count($messages) == 1) ?
                  $transKey . ": " . $value . "<br>" :
                  $transKey . ": " . $value . "<br>";
            }
         }
         $result = (object) [
            'http_code' => 200,
            'status' => 'error',
            'code' => 'E010',
            'message' => $msg,
         ];
         return $result;
      }
   }


   public static function loggingApp($ip, $idUser, $action)
   {
      try {
         $url = url()->current();
         $actionBundle = $action . ' access url: ' . $url;
         $saveLogs = DB::table('logs_apps')->insert([
            'user_id' => $idUser ?? null,
            'action' => $actionBundle ?? 'unkown action',
            'ip' => $ip ?? '999.999.999.999',
            'created_at' => Carbon::now(),
         ]);
         return;
      } catch (\Throwable $th) {
         session()->flash('notifikasi', [
            "icon" => config('global.errors.E501.status'),
            "title" => config('global.errors.E501.code'),
            "message" => config('global.errors.E501.message'),
        ]);
      }
   }

   public static function generatedCode($name,$number)
   {
    $words = explode(" ", $name);
    // $acronym = "";

    $first = mb_substr($words[0], 0, 1);
    $second = "";
    if(isset($words[1])) {
        $second = mb_substr($words[1], 0, 1);
    }

    $code = strtoupper($first).strtoupper($second).sprintf("%03d", $number);

    return $code;

   }

   public static function setCurrency($value)
    {
        $result = number_format($value, 2, ",", ".");
        return $result;
    }
}
