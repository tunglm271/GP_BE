<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Services\NotificationService;
class TransactionController extends Controller
{

    public function showBill(Request $request, Appointment $appointment) {
        $bill = $appointment->bill->load(['services','medicalFacility']);
        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }
        return response()->json($bill);
    }

    public function vnpay_payment(Request $request) {
        $appointmentId = $request->input('appointment_id');
        $bill = Bill::where('appointment_id', $appointmentId)->first();
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:5173/appointments/{$appointmentId}/bill/payment-result";
        $vnp_TmnCode = "1B2XO33D";//Mã website tại VNPAY 
        $vnp_HashSecret = "Z22NW36OY0GGHH2TRL9P9XLRB7E9RNGD"; //Chuỗi bí mật
        
        $vnp_TxnRef = $request->input('appointment_id'); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Thanh toán đơn khám";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $bill->total_amount  * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );
        
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }
        
        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
            , 'message' => 'success'
            , 'data' => $vnp_Url);
            if (isset($_POST['redirect'])) {
                header('Location: ' . $vnp_Url);
                die();
            } else {
                echo json_encode($returnData);
            }
    }

    public function verifyPayment(Request $request, $appointmentId) {
        $data = $request->all();
        if(isset($data['vnp_ResponseCode']) && $data['vnp_ResponseCode'] == '00') {
            $bill = Bill::where('appointment_id', $appointmentId)->first();
            if ($bill && $bill->status != 'paid') {
                $bill->status = 'paid';
                $bill->payment_date = now();
                $bill->transaction_id = $data['vnp_TxnRef'];
                $bill->bank_code = $data['vnp_BankCode'];
                $bill->card_type = $data['vnp_CardType'];
                $bill->save();
                $appointment = $bill->appointment;
                $appointment->status = 'paid';
                $appointment->save();


                broadcast(new \App\Events\BillPaidEvent($appointment));

                $userId = $appointment->medicalFacility->user_id;
                $patientName = $appointment->healthProfile->name;
                NotificationService::sendToUser($userId,[
                    'type' => 'bill_paid',
                    'title' => 'Yêu cầu khám mới',
                    'message' => "Bệnh nhân $patientName đã thanh toán hóa đơn",
                    'data' => [
                        'appointment_id' => $appointment->id,
                    ],
                ]);

                return response()->json(['message' => 'Payment successful'], 200);
            }
        }
        return response()->json(['message' => 'Payment failed'], 400);
    }

    public function getRevenueStats(Request $request) {
        $facility = $request->user()->medicalFacility;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $bills = Bill::where('medical_facility_id', $facility->id)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();
        $totalRevenue = $bills->sum('total_amount');
        $totalPaid = $bills->where('status', 'paid')->sum('total_amount');
        $totalUnpaid = $bills->where('status', 'unpaid')->sum('total_amount');
        $totalAppointments = $bills->count();
        $totalPaidAppointments = $bills->where('status', 'paid')->count();
        $totalUnpaidAppointments = $bills->where('status', 'unpaid')->count();

        // Calculate daily revenue in array format for chart
        $dailyRevenue = $bills->where('status', 'paid')
            ->groupBy(function($bill) {
                return \Carbon\Carbon::parse($bill->payment_date)->format('Y-m-d');
            })
            ->map(function($group, $date) {
                return [
                    'date' => $date,
                    'revenue' => $group->sum('total_amount')
                ];
            })
            ->values();
        
        $topServices = $facility->services
                ->sortByDesc(function ($service) {
                    return $service->totalPaidAmount();
                })
                ->take(3)  
                ->map(function ($service) {
                    return [
                        'name' => $service->name,
                        'revenue' => $service->totalPaidAmount(),
                    ];
                })
                ->values();
                    
        

        return response()->json([
            'bills' => $bills->load(['healthProfile', 'services']),
            'total_revenue' => $totalRevenue,
            'total_paid' => $totalPaid,
            'total_unpaid' => $totalUnpaid,
            'total_appointments' => $totalAppointments,
            'total_paid_appointments' => $totalPaidAppointments,
            'total_unpaid_appointments' => $totalUnpaidAppointments,
            'daily_revenue' => $dailyRevenue,
            'top_services' => $topServices,
        ]);
    }
}
