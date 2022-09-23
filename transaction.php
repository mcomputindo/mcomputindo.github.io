<?php
require_once "config.php";
header("Access-Control-Allow-Origin: *");
if(function_exists($_GET['function'])) {
         $_GET['function']();
      }  
   
   function get_notrans()
   {
      global $conn;
      if (!empty($_GET["notrans"])) {
         $notrans = $_GET["notrans"]; 
         //$bayar= $_GET["bayar"];     
      } 

      $data = pg_query($conn, "SELECT count(*) as jml from transaksi_parkir where no_pol='$notrans' and status=1 " );
       $d = pg_fetch_assoc($data);
         $NMR = $d["jml"];

      if($NMR>=1){

       $sql =  pg_query($conn,"SELECT a.id, a.no_pol,to_char(waktu_masuk,'DD-MM-YYYY hh:mi:ss') as waktu_masuk, b.nama       as id_kendaraan, c.nama as pintu_masuk, to_char(now(),'DD-MM-YYYY hh:mi:ss') as saatini, a.id_pintu_masuk, bayar_keluar FROM transaksi_parkir a left join jenis_mobil b on (b.id=a.id_kendaraan) left join nama_pos c on (c.id=a.id_pintu_masuk) where no_pol='$notrans' order by waktu_masuk desc limit 1");
                    $d = pg_fetch_assoc($sql);
                    $masuk = $d["waktu_masuk"];
                    $saatini=$d["saatini"];
                    $bayar=$d["bayar_keluar"];
                    $kend=$d["id_kendaraan"];
                    $no=$d["no_pol"];
                    $pm=$d["id_pintu_masuk"];
                    $namapm=$d["pintu_masuk"];
                    $pmnya=$pm.'-'.$namapm;

         $sql1 =  pg_query($conn,"SELECT * from fntariftfunc('$notrans', null) as f(ticket text, payload text, status text, intime timestamp, 
                       duration int, tarif int, vehtype text, outtime timestamp, grace_period int, location_name text, paystat text)");
                       $d = pg_fetch_assoc($sql1);
                       $byr=$d["tarif"];

          $data = pg_query($conn, "SELECT namaperusahaan, namalokasi, namasystem from softseting" );
                            $d = pg_fetch_assoc($data);
                            $lokasi = $d["namalokasi"];
                            $sistem = $d["namasystem"];



      if (($sql)AND((int)$bayar==0)){
        $response=array(
                     'status' => 0,
                     'message' =>'Success',
                     'bayar' => (int)$byr,
                     'notrans' =>$no, 
                     'waktu_masuk' => $masuk,
                     'saat_ini' => $saatini,
                     'kendaraan' => $kend,
                     'id_pintu_masuk' =>$pmnya,
                     'lokasi' => $lokasi
                     
                  );
      }else if (($sql)AND((int)$bayar<>0)){
         $response=array(
                     'status' => 1,
                     'message' =>'Success',
                     'bayar' => (int)$byr,
                     'notrans' =>$no, 
                     'waktu_masuk' => $masuk,
                     'saat_ini' => $saatini,
                     'kendaraan' => $kend,
                     'id_pintu_masuk' => $pm,
                     'lokasi' => $lokasi
                  );


      }
   }
      else
      {
         $response=array(
                     'status' => 2,
                     'message' =>'No Data Found'
                  );
   
                    
   }
      header('Content-Type: application/json');
      echo json_encode($response);
       
   }
   
 ?>
